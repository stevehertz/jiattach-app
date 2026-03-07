<?php

namespace App\Traits;

trait EnumHelpers
{
    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all names as array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get an array of [value => label] for select dropdowns
     */
    public static function options(): array
    {
        $cases = self::cases();
        $options = [];

        foreach ($cases as $case) {
            $options[$case->value] = method_exists($case, 'label')
                ? $case->label()
                : ucfirst(str_replace('_', ' ', $case->value));
        }

        return $options;
    }

    /**
     * Get an array of [value => color] for badges
     */
    public static function colors(): array
    {
        $cases = self::cases();
        $colors = [];

        foreach ($cases as $case) {
            if (method_exists($case, 'color')) {
                $colors[$case->value] = $case->color();
            }
        }

        return $colors;
    }

    /**
     * Get an array of [value => icon] for icons
     */
    public static function icons(): array
    {
        $cases = self::cases();
        $icons = [];

        foreach ($cases as $case) {
            if (method_exists($case, 'icon')) {
                $icons[$case->value] = $case->icon();
            }
        }

        return $icons;
    }

    /**
     * Check if a given value is valid
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }

    /**
     * Get the enum instance from a value
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get random enum case
     */
    public static function random(): self
    {
        $cases = self::cases();
        return $cases[array_rand($cases)];
    }
}
