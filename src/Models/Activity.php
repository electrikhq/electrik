<?php

namespace Electrik\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Human-readable label for Electrik team events.
     */
    public function descriptionLabel(): string
    {
        $action = $this->event ?: $this->description;
        $properties = $this->properties;
        $props = $properties instanceof \Illuminate\Support\Collection
            ? $properties->toArray()
            : (is_array($properties) ? $properties : []);

        $props = array_merge([
            'name' => $this->causer?->name ?? null,
            'email' => $props['email'] ?? null,
            'role' => $props['role'] ?? null,
        ], $props);

        $key = 'electrik::electrik.activity.'.$action;
        $translated = __($key, $props);

        if ($translated === $key) {
            $fallback = 'electrik.activity.'.$action;
            $translated = __($fallback, $props);
            if ($translated === $fallback) {
                return str_replace(['_', '.'], ' ', (string) $action);
            }
        }

        return $translated;
    }
}
