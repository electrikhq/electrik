<?php

namespace Electrik\Support;

class Countries
{
    /**
     * ISO 3166-1 alpha-2 code => localized English name.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        static $cache;

        if (is_array($cache)) {
            return $cache;
        }

        $items = [];

        if (class_exists(\Symfony\Component\Intl\Countries::class)) {
            foreach (\Symfony\Component\Intl\Countries::getNames('en') as $code => $name) {
                $items[strtoupper($code)] = $name;
            }
        } elseif (function_exists('locale_get_display_region')) {
            foreach (range('A', 'Z') as $a) {
                foreach (range('A', 'Z') as $b) {
                    $code = $a.$b;
                    $name = locale_get_display_region('und_'.$code, 'en');

                    if (! is_string($name) || $name === '' || $name === $code || str_starts_with($name, 'und_')) {
                        continue;
                    }

                    $items[$code] = $name;
                }
            }
        }

        if ($items === []) {
            $items = self::fallback();
        }

        asort($items, SORT_NATURAL | SORT_FLAG_CASE);

        return $cache = $items;
    }

    /**
     * @return array<string, string>
     */
    protected static function fallback(): array
    {
        return [
            'AU' => 'Australia',
            'BR' => 'Brazil',
            'CA' => 'Canada',
            'DE' => 'Germany',
            'ES' => 'Spain',
            'FR' => 'France',
            'GB' => 'United Kingdom',
            'IN' => 'India',
            'JP' => 'Japan',
            'MX' => 'Mexico',
            'NL' => 'Netherlands',
            'SG' => 'Singapore',
            'US' => 'United States',
        ];
    }
}
