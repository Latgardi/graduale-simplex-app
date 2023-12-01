<?php

namespace GradualeSimplex\LiturgicalCalendar\Utility;

use InvalidArgumentException;

class RomanNumber {
    public final const array ROMAN_VALUES = [
        'M' => 1000,
        'D' => 500,
        'C' => 100,
        'L' => 50,
        'X' => 10,
        'V' => 5,
        'I' => 1,
    ];
    private const array ROMAN_ZERO = ['N', 'nulla'];
    private const string ROMAN_REGEX = '/\b(?=[MDCLXVI])(M{0,3})(C[DM]|D?C{0,3})(X[LC]|L?X{0,3})(I[VX]|V?I{0,3})\b/';
    private int|string $number;

    public function __construct(string|int $number)
    {
        if (is_string($number) && !self::isRomanNumber(roman: $number)) {
            throw new InvalidArgumentException(message: 'Roman number is not valid.');
        }
        $this->number = $number;
    }

    public static function excludeFromString(string $string): ?self
    {
        preg_match_all(self::ROMAN_REGEX, $string, $matches);
        if (is_array($matches)) {
            foreach ($matches as $group) {
                foreach ($group as $match) {
                    try {
                        return new self(number: $match);
                    } catch (InvalidArgumentException) {
                        continue;
                    }
                }
            }
        }
        return null;
    }

    public static function isRomanNumber($roman): bool
    {
        return preg_match(pattern: self::ROMAN_REGEX, subject: $roman) > 0;
    }

    public function getIntValue(): int
    {
        if (is_int($this->number)) {
            return $this->number;
        }
        if (in_array($this->number, self::ROMAN_ZERO, true)) {
            return 0;
        }

        $result = 0;

        for ($i = 0, $length = strlen($this->number); $i < $length; $i++) {
            $value = self::ROMAN_VALUES[$this->number[$i]];
            $nextValue = !isset($roman[$i + 1]) ? null : self::ROMAN_VALUES[$roman[$i + 1]];
            $result += (!is_null($nextValue) && $nextValue > $value) ? -$value : $value;
        }
        return $result;
    }

    public function getRomanValue(): string
    {
        if (is_string($this->number)) {
            return $this->number;
        }
        $result = '';
        $number = $this->number;
        foreach(self::ROMAN_VALUES as $roman => $value) {
            $matches = (int) ($number/$value);
            $result .= str_repeat($roman, $matches);
            $number %= $value;
        }

        return $result;
    }
}