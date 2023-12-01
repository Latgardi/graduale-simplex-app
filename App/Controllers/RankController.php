<?php

namespace App\Controllers;

use App\Database\QueryBuilder;
use App\Localization\LocalizedName;
use App\View\Title;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;

class RankController extends BaseController
{
    private const string CHANTS_URL_PREFIX = '/chants/';
    private string $URLPrefix;
    private CelebrationRank $rank;
    private string $title;

    public function __construct(CelebrationRank $rank)
    {
        if ($rankSlug = $this->getRankSlug(rank: $rank)) {
            $this->URLPrefix = self::CHANTS_URL_PREFIX . $rankSlug . '/';
            $this->rank = $rank;
            $this->title = LocalizedName::for($rankSlug) ?? $rank->name;
        }
    }

    public function celebrations(): void
    {
        Title::set($this->title);
        $queryBuilder = new QueryBuilder();
        $days = $queryBuilder->getCelebrationsOfRank(rank: $this->rank);
        $this->renderCelebrationList(celebrations: $days, URLPrefix: $this->URLPrefix);
    }

    public function celebration(string $slug, bool $setTitle = true): void
    {
        $queryBuilder = new QueryBuilder();
        $pdo = $queryBuilder->getCelebrationOfRank(rank: $this->rank, slug: $slug);
        $this->renderLiturgicalDay(dayPDO: $pdo, setTitle: $setTitle);
    }

    private function getRankSlug(CelebrationRank $rank): ?string
    {
        return match ($rank) {
            CelebrationRank::Sollemnitas => 'solemnities',
            CelebrationRank::Festum => 'feasts',
            CelebrationRank::Memoria,
            CelebrationRank::Commemoratio,
            CelebrationRank::MemoriaAdLibitum => 'memorials',
            default => null
        };
    }
}