<?php

namespace App\Localization;
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
use App\Localization\Exception\LocalizationInitException;

class LocalizedName
{
    public static array $strings;
    public static bool $isInitialized = false;

    /**
     * @param array<string> $JSONLocalizedStringsFiles
     * @return void
     * @throws LocalizationInitException
     */
    public static function init(array $JSONLocalizedStringsFiles): void
    {
        try {
            self::$strings = [];
            foreach ($JSONLocalizedStringsFiles as $fileName) {
                $data = file_get_contents($fileName);
                self::$strings += json_decode($data, true, 512, JSON_THROW_ON_ERROR);
            }
            self::$isInitialized = true;
        } catch (\JsonException) {
           self::$strings = [];
        } catch (\ValueError) {
            throw new LocalizationInitException('Invalid file path.');
        }
    }

    public static function for(string $string): ?string
    {
        try {
            self::checkInit();
            return
                self::$strings[strtolower($string)]
                ?? self::$strings[strtoupper($string)]
                ?? self::$strings[$string]
                ?? null;
        } catch (LocalizationInitException) {
            return null;
        }
    }

    private static function checkInit()
    {
        if (!self::$isInitialized) {
            throw new LocalizationInitException('Localization should be inited first.');
        }
    }
}
