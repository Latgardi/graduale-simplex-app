<?php

namespace App\Database;

use App\Database\Type\PrimaryLiturgicalDayMassResult;
use App\Database\Type\PrimaryLiturgicalDayResult;
use App\Lib\Enum\LiturgyPart;
use FTP\Connection;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;
use PDOStatement;

class QueryBuilder
{
    private const string CELEBRATIONS_TABLE_NAME = 'celebrations';
    private const string CHANTS_TABLE_NAME = 'chants';
    private const array CHANT_FIELDS = ['title', 'description', 'verset'];
    private const string BASE_CELEBRATION_SELECT = 'title, slug';

    public function getSeasonDays(Season $season): ?PDOStatement
    {
        $select = self::BASE_CELEBRATION_SELECT;
        $query = "select $select from "
                    . self::CELEBRATIONS_TABLE_NAME
                  . " where season is '$season->name'
                   and {$this->notNullChants()} 
                  ";
        $pdo = Connector::getInstance()->getPDO();
        if (!is_bool($pdo)) {
            return $pdo->query($query);
        }
        return null;
    }

    public function getPrimaryLiturgicalDayMass(string $daySlug, string $massSlug): ?PrimaryLiturgicalDayMassResult
    {
        $pdo = Connector::getInstance()->getPDO();
        $query = "select masses.*, c.slug as daySlug, c.title as dayTitle, c.rank as rank
            from masses 
            join celebrations c on c.id = masses.celebration 
            where masses.slug is :massSlug and daySlug is :daySlug
            ";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['massSlug' => $massSlug, 'daySlug' => $daySlug]);
        $mass = $stmt->fetch();
        if (is_bool($mass)) {
            return null;
        }
        $title = $mass['title'];
        $dayTitle = $mass['dayTitle'];
        $rank = CelebrationRank::tryFromString($mass['rank']);
        $introitusID = $mass['introitus'];
        $offertoriumID = $mass['offertorium'];
        $communioID = $mass['communio'];
        $IDs = [];
        foreach ([$introitusID, $offertoriumID, $communioID] as $id) {
            if (!is_null($id)) {
                $IDs[] = $id;
            }
        }
        $stringIDs = implode(',', $IDs);
        $query = "select * from chants where id in ($stringIDs)";
        if ($result = $pdo->query($query)) {
            return new PrimaryLiturgicalDayMassResult(
                pdo: $result,
                massTitle: $title,
                dayTitle: $dayTitle,
                dayRank: $rank
            );
        }
        return null;
    }

    public function getPrimaryLiturgicalDay(string $slug): ?PrimaryLiturgicalDayResult
    {
        $pdo = Connector::getInstance()->getPDO();
        $stmt = $pdo->prepare('select * from celebrations where slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $day = $stmt->fetch();
        if (is_bool($day)) {
            return null;
        }
        if ($day['has_multiple_masses'] !== 1) {
            return new PrimaryLiturgicalDayResult(
                title: $day['title'],
                pdo: $this->getCelebrationOfRank(rank: CelebrationRank::DiesLiturgiciPrimarii, slug: $slug),
                hasMultipleMasses: false
            );
        }

        $query = "select title, slug from masses where celebration is {$day['id']}";
        if ($result = $pdo->query($query)) {
            return new PrimaryLiturgicalDayResult(
                title: $day['title'],
                pdo: $result,
                hasMultipleMasses: true
            );
        }
        return null;

    }

    public function getCelebrationsOfRank(CelebrationRank $rank): ?PDOStatement
    {
        $rankName = strtolower($rank->value);
        $select = self::BASE_CELEBRATION_SELECT;
        $where = " where (rank is '$rankName'";
        if ($rank === CelebrationRank::Memoria) {
            $optionalMemorialName = strtolower(CelebrationRank::MemoriaAdLibitum->value);
            $commemorationName = strtolower(CelebrationRank::Commemoratio->value);
            $where .= " or rank is '$optionalMemorialName' or rank is '$commemorationName'";
        }
        $where .= ") ";
        $query = "select $select from "
            . self::CELEBRATIONS_TABLE_NAME
            . $where
            . "and {$this->notNullChants()}";
        if ($rank === CelebrationRank::DiesLiturgiciPrimarii) {
            $query .= " and season is not 'Advent' or has_multiple_masses is true";
        }
        $pdo = Connector::getInstance()->getPDO();
        if ($result = $pdo->query($query)) {
            return $result;
        }
        return null;
    }

    public function getCelebrationOfRank(CelebrationRank $rank, string $slug): ?PDOStatement
    {
        $rankName = strtolower($rank->value);
        $select = $this->getChantsSelect(
            celebrationsTable: self::CELEBRATIONS_TABLE_NAME,
            chantsTable: self::CHANTS_TABLE_NAME
        );
        $where = " where (rank is '$rankName'";
        if ($rank === CelebrationRank::Memoria) {
            $optionalMemorialName = strtolower(CelebrationRank::MemoriaAdLibitum->value);
            $commemorationName = strtolower(CelebrationRank::Commemoratio->value);
            $where .= " or rank is '$optionalMemorialName' or rank is '$commemorationName'";
        }
        $where .= ") ";
        $query = $select
            . $where
            . "and slug is :slug "
            . "and {$this->notNullChants()} ";
        $pdo = Connector::getInstance()->getPDO();
        $stmt = $pdo->prepare($query);
        $stmt->execute(['slug' => $slug]);
        if ($result = $stmt) {
            return $result;
        }
        return null;
    }

    public function getSeasonDay(Season $season, string $slug): ?PDOStatement
    {
        $select = $this->getChantsSelect(
            celebrationsTable: self::CELEBRATIONS_TABLE_NAME,
            chantsTable: self::CHANTS_TABLE_NAME
        );
        $query = $select . "where season is '$season->name'
                   and slug is :slug
                   and {$this->notNullChants()} ";
        $pdo = Connector::getInstance()->getPDO();
        $stmt = $pdo->prepare($query);
        $stmt->execute(['slug' => $slug]);
        if ($result = $stmt) {
            return $result;
        }
        return null;
    }

    private function getChantsSelect(string $celebrationsTable, string $chantsTable): string
    {
        $select = 'select c.title, c.rank, ';
        $partsLastIndex = count(LiturgyPart::cases()) - 1;
        foreach (LiturgyPart::cases() as $part) {
            $partName = strtolower($part->name);
            $select .= "c.$partName, ";
        }
        $lastFieldIndex = count(self::CHANT_FIELDS) - 1;
        foreach (LiturgyPart::cases() as $index => $case) {
            $partName = strtolower($case->name);
            foreach (self::CHANT_FIELDS as $fieldIndex => $field) {
                $tableNumber = 'c' . $index;
                $select .= "$tableNumber.$field as {$partName}_$field";
                if ($index === $partsLastIndex && $fieldIndex === $lastFieldIndex) {
                    $select .= ' ';
                } else {
                    $select .= ', ';
                }
            }
        }
        $select .= "from $celebrationsTable as c  ";
        foreach (LiturgyPart::cases() as $index => $part) {
            $partName = strtolower($part->name);
            $tableNumber = 'c' . $index;
            $select .= "left join $chantsTable $tableNumber on $tableNumber.id = c.$partName ";
        }
        return $select;
    }
    private function notNullChants(): string
    {
        return '(introitus is not null 
        or offertorium is not null 
        or communio is not null)';
    }
}