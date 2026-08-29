<?php

namespace Electrik\Support;

class Locales
{
    /**
     * @return array<string, array{label: string, dir: string}>
     */
    public static function definitions(): array
    {
        return [
            'en' => ['label' => 'English', 'dir' => 'ltr'],
            'es' => ['label' => 'Español', 'dir' => 'ltr'],
            'fr' => ['label' => 'Français', 'dir' => 'ltr'],
            'ar' => ['label' => 'العربية', 'dir' => 'rtl'],
            'hi' => ['label' => 'हिन्दी', 'dir' => 'ltr'],
        ];
    }

    /**
     * @return array<string, string> code => native label
     */
    public static function options(): array
    {
        return collect(static::definitions())
            ->mapWithKeys(fn (array $meta, string $code) => [$code => $meta['label']])
            ->all();
    }

    public static function codes(): array
    {
        return array_keys(static::definitions());
    }

    public static function isSupported(?string $locale): bool
    {
        return is_string($locale) && isset(static::definitions()[$locale]);
    }

    public static function direction(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return static::definitions()[$locale]['dir'] ?? 'ltr';
    }

    public static function isRtl(?string $locale = null): bool
    {
        return static::direction($locale) === 'rtl';
    }
}
