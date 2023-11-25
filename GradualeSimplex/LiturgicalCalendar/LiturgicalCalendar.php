<?php

namespace GradualeSimplex\LiturgicalCalendar;
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use App\Lib\Utility\NameConverter;
use App\Localization\LocalizedLiturgicalDay;
use GradualeSimplex\LiturgicalCalendar\Enum\AliasWeek;
use GradualeSimplex\LiturgicalCalendar\Enum\CelebrationRank;
use GradualeSimplex\LiturgicalCalendar\Enum\ConfigEntry;
use GradualeSimplex\LiturgicalCalendar\Enum\Endpoint;
use GradualeSimplex\LiturgicalCalendar\Enum\ExceptionWeek;
use GradualeSimplex\LiturgicalCalendar\Enum\Language;
use GradualeSimplex\LiturgicalCalendar\Enum\LiturgicalColour;
use GradualeSimplex\LiturgicalCalendar\Enum\Season;
use GradualeSimplex\LiturgicalCalendar\Enum\Weekday;
use GradualeSimplex\LiturgicalCalendar\Type\Celebration;
use GradualeSimplex\LiturgicalCalendar\Type\DateTimeExt;
use GradualeSimplex\LiturgicalCalendar\Type\LiturgicalDay;
use GradualeSimplex\LiturgicalCalendar\Utility\Config;


class LiturgicalCalendar
{
    public const API_URL = "http://calapi.inadiutorium.cz/api/v0/la/calendars/general-la";
    private const TODAY_ENDPOINT = '/today';
    private const CHANTS_PATH = '/chants/';
    private const CHANTS_STORAGE_PATH = '/library/';
    private const LOCAL_DATA_FOLDER = '/data/calendar/';
    private const DEFAULT_CONFIG_PATH = __DIR__ . '/config.json';
    private const ADDITIONAL_CELEBRATIONS_ENTRY_DATE_FORMAT = 'n-j';
    private ?array $additionalCelebrations = null;
    private ?Config $config = null;

    public function __construct(string $configJSONPath = self::DEFAULT_CONFIG_PATH)
    {
        $isAbsolute = $configJSONPath === self::DEFAULT_CONFIG_PATH;
        $config = new Config(configJSONPath: $configJSONPath, isAbsolute: $isAbsolute);
        if (!$config->isEmpty()) {
            $this->config = $config;
            if ($additionalCelebrationsFilePaths = $this->config->get(ConfigEntry::AdditionalCelebritiesJSONFiles)) {
                $this->additionalCelebrations = $this->getAdditionalCelebrations($additionalCelebrationsFilePaths);
            }
        }
    }

    public function getToday(Language $language): ?LiturgicalDay
    {
        $JSONData = $this?->getJSONData(endpoint: self::TODAY_ENDPOINT);
        $day = $this?->getLiturgicalDayFromJSONObject(JSONObject: $JSONData);
        if (!is_null($day)) {
            return match ($language) {
                Language::Latin => $day,
                Language::Belarusian => (new LocalizedLiturgicalDay(day: $day))->getDay()
            };
        }
        return null;
    }

    public function getCalendar(int $year, int $month): array
    {
        $calendar = [];
        $endpoint = $this->constructEndpoint(year: $year, month: $month);

        $JSONData = $this->getStoredCalendar($year, $month);
        if (is_null($JSONData)) {
            $JSONData = $this->getJSONData(endpoint: $endpoint);
            $this->saveCalendar($JSONData, $year, $month);
        }

        if (is_array($JSONData)) {
            foreach ($JSONData as $day) {
                $calendar[] = $this->getLiturgicalDayFromJSONObject(JSONObject: $day);
            }
        }
        return $calendar;
    }

    private function getAdditionalCelebrations(array $JSONFilePaths): array
    {
        $celebrations = [];
        foreach ($JSONFilePaths as $filePath) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filePath)) {
                $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . $filePath);
                $celebrationsJSON = $this->getJSONFromData($data);
                if (is_array($celebrationsJSON)) {
                    foreach ($celebrationsJSON as $celebration) {
                        $celebrations[$celebration->date][] = new Celebration(
                            title: $celebration->title,
                            colour: LiturgicalColour::tryFromString($celebration->colour),
                            rank: CelebrationRank::tryFromString($celebration->rank),
                            comment: $celebration->comment ?? null
                        );
                    }
                }
            }
        }
       return $celebrations;
    }

    private function isSetConfig(): bool
    {
        return !is_null($this->config);
    }

    private function saveCalendar(array $data, int $year, int $month): void
    {
        $fileName = $year . '_' . $month . '.json';
        $dirPath = $_SERVER['DOCUMENT_ROOT'] . self::LOCAL_DATA_FOLDER;
        if (is_dir($dirPath)) {
            $data = json_encode($data, JSON_THROW_ON_ERROR);
            file_put_contents($dirPath . '/' . $fileName, $data);
        }
    }

    private function getStoredCalendar(int $year, int $month): ?array
    {
        $fileName = $year . '_' . $month . '.json';
        $dirPath = $_SERVER['DOCUMENT_ROOT'] . self::LOCAL_DATA_FOLDER;
        if (is_dir($dirPath) && file_exists($dirPath . $fileName)) {
            $data = file_get_contents($dirPath . $fileName);
            return $this->getJSONFromData($data);
        }
        return null;
    }

    private function getJSONData(string $endpoint): null|object|array
    {
        try {
            $url = self::API_URL . $endpoint;
            $data = file_get_contents($url);
            return $this->getJSONFromData($data);
        } catch (\JsonException) {
            return null;
        }
    }

    private function getJSONFromData(string $data): object|array
    {
        return json_decode($data, false, 512, JSON_THROW_ON_ERROR);
    }

    private function getLiturgicalDayFromJSONObject(object $JSONObject): ?LiturgicalDay
    {
        try {
            $date = new DateTimeExt();
            $date->setTimestamp(strtotime($JSONObject->date));
            $season = Season::tryFromString($JSONObject->season);
            $seasonWeek = (int)$JSONObject->season_week;
            $celebrations = [];
            foreach ($JSONObject->celebrations as $JSONCelebration) {
                if ($celebration = $this->getCelebrationFromJSONObject(celebration: $JSONCelebration)) {
                    $celebrations[] = $celebration;
                }
            }
            if (!is_null($this->additionalCelebrations)) {
                $additionalCelebrationsEntry = $date->format(self::ADDITIONAL_CELEBRATIONS_ENTRY_DATE_FORMAT);
                if (
                    $celebrations[0]->rankNum > 1.3
                    && isset($this->additionalCelebrations[$additionalCelebrationsEntry])
                ) {
                    $celebrations = array_merge($celebrations, $this->additionalCelebrations[$additionalCelebrationsEntry]);
                }
            }
            foreach ($celebrations as $celebration) {
                $celebration->chantLink = $this->getChantURLForCelebration(
                    celebration: $celebration,
                    season: $season,
                    week: $seasonWeek
                );
            }
            $weekday = Weekday::tryFromString($JSONObject->weekday);

            return new LiturgicalDay(
                date: $date,
                season: $season,
                seasonWeek: $seasonWeek,
                celebrations: $celebrations,
                weekday: $weekday
            );
        } catch (\Error|\Exception) {
            return null;
        }
    }

    private function getChantURLForCelebration(
        Celebration $celebration,
        ?Season     $season = null,
        int         $week = null
    ): ?string
    {
        $areNullSeasonAndWeek = is_null($season) && is_null($week);
        switch ($celebration->rank) {
            case CelebrationRank::TriduumPaschale:
                return $this->getChantURLForEasterTriduum($celebration);
            case CelebrationRank::Festum:
                return $this->getChantURLForFeast($celebration);
            case CelebrationRank::Memoria:
                return $this->getChantURLForMemorial($celebration);
            case CelebrationRank::Sollemnitas:
                if (!$areNullSeasonAndWeek) {
                    if (is_null(AliasWeek::tryNamed("{$season->name}_$week"))) {
                        return $this->getChantURLForSolemnity($celebration);
                    }
                    return $this->getChantURLForRegularWeek($season, $week);
                }
                return $this->getChantURLForSolemnity($celebration);
            case CelebrationRank::DiesLiturgiciPrimarii:
                if (!$areNullSeasonAndWeek) {
                    if (is_null(ExceptionWeek::tryNamed("{$season->name}_$week"))) {
                        return $this->getChantURLForPrimaryLiturgicalDays($celebration);
                    }
                    return $this->getChantURLForRegularWeek($season, $week);
                }
                return $this->getChantURLForPrimaryLiturgicalDays($celebration);
            default:
                if (!$areNullSeasonAndWeek) {
                    return $this->getChantURLForRegularWeek($season, $week);
                }
        }
        return null;
    }

    private function getChantURLForRegularWeek(Season $season, $week): ?string
    {
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'seasons/' . strtolower($season->name) . '/' . $week;
        $URL = self::CHANTS_PATH . 'seasons/' . strtolower($season->name) . '/' . $week;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLForSolemnity(Celebration $celebration): ?string
    {
        $webTitle = NameConverter::titleToWeb($celebration->title);
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'solemnities/' . $webTitle;
        $URL = self::CHANTS_PATH . 'solemnities/' . $webTitle;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLForEasterTriduum(Celebration $celebration): ?string
    {
        $webTitle = NameConverter::titleToWeb($celebration->title);
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'easter-triduum/' . $webTitle;
        $URL = self::CHANTS_PATH . 'easter-triduum/' . $webTitle;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLForPrimaryLiturgicalDays(Celebration $celebration): ?string
    {
        $webTitle = NameConverter::titleToWeb($celebration->title);
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'primary-liturgical-days/' . $webTitle;
        $URL = self::CHANTS_PATH . 'primary-liturgical-days/' . $webTitle;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLForFeast(Celebration $celebration): ?string
    {
        $webTitle = NameConverter::titleToWeb($celebration->title);
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'feasts/' . $webTitle;
        $URL = self::CHANTS_PATH . 'feasts/' . $webTitle;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLForMemorial(Celebration $celebration): ?string
    {
        $webTitle = NameConverter::titleToWeb($celebration->title);
        $storageDir = $_SERVER['DOCUMENT_ROOT'] . self::CHANTS_STORAGE_PATH . 'memorials/' . $webTitle;
        $URL = self::CHANTS_PATH . 'memorials/' . $webTitle;

        return $this->getChantURLifExists($storageDir, $URL);
    }

    private function getChantURLifExists(string $absolutePath, string $URL): ?string
    {
        if (is_dir($absolutePath)) {
            return $URL;
        }
        return null;
    }


    private function getCelebrationFromJSONObject(object $celebration): ?Celebration
    {
        try {
            $title = $celebration->title;
            $colour = LiturgicalColour::tryFromString($celebration->colour);
            $rank = CelebrationRank::tryFromString($celebration->rank);
            $rankNum = $celebration->rank_num;
            return new Celebration(
                title: $title,
                colour: $colour,
                rank: $rank,
                rankNum: $rankNum
            );
        } catch (\Error|\Exception) {
            return null;
        }
    }

    private function constructEndpoint(
        int|string|null $year = null,
        int|string|null $month = null,
        int|string|null $day = null
    ): string
    {
        $link = '/';
        if (!is_null($year)) {
            $link .= $year;
            if (!is_null($month)) {
                $link .= '/' . $month;
                if (!is_null($day)) {
                    $link .= '/' . $day;
                }
            }
        }
        return $link;
    }
}
