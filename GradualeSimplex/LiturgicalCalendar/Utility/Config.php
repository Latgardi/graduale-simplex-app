<?php

namespace GradualeSimplex\LiturgicalCalendar\Utility;

use GradualeSimplex\LiturgicalCalendar\Enum\ConfigEntry;
use GradualeSimplex\LiturgicalCalendar\Exception\IncorrectConfigFileException;

class Config
{
    private array $data = [];
    public function __construct(
        public $configJSONPath, bool $isAbsolute = false
    )
    {
        $this->parseConfigFile($this->configJSONPath, $isAbsolute);
    }

    public function get(ConfigEntry $entry): mixed
    {
        return $this->data[$entry->name] ?? null;
    }

    public function isEmpty(): bool
    {
        return count($this->data) === 0;
    }

    private function parseConfigFile(string $filePath, bool $isAbsolute): void
    {
        $filePath = $isAbsolute ? $filePath : $_SERVER['DOCUMENT_ROOT'] . $filePath;
        if (file_exists($filePath)) {
            $this->processConfigFile($filePath);
        } else {
            throw new IncorrectConfigFileException('config file missing');
        }
    }

    private function processConfigFile(string $absolutePath): void
    {
        $data = file_get_contents($absolutePath);
        $configEntries = null;
        $badFileException = new IncorrectConfigFileException('bad JSON file');
        try {
            $configEntries = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw $badFileException;
        }
        foreach (ConfigEntry::cases() as $entry) {
            if (isset($configEntries->{$entry->name})) {
                $this->data[$entry->name] = $configEntries->{$entry->name};
            }
        }

    }
}