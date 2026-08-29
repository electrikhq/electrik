<?php

namespace Electrik\Concerns;

use Electrik\Models\Activity;
use Illuminate\Support\Str;
use Spatie\PersonalDataExport\PersonalDataSelection;

trait ExportsElectrikPersonalData
{
    public function selectPersonalData(PersonalDataSelection $personalDataSelection): void
    {
        $personalDataSelection->add('user.json', [
            'id' => $this->getKey(),
            'name' => $this->name,
            'email' => $this->email,
            'timezone' => $this->timezone ?? null,
            'locale' => $this->locale ?? null,
            'created_at' => optional($this->created_at)?->toIso8601String(),
        ]);

        if (method_exists($this, 'teams')) {
            $teams = $this->teams()->get()->map(fn ($team) => [
                'id' => $team->getKey(),
                'name' => $team->name,
                'owner_id' => $team->owner_id ?? null,
            ])->values()->all();

            $personalDataSelection->add('teams.json', $teams);
        }

        if (class_exists(Activity::class)) {
            $activity = Activity::query()
                ->where('causer_type', $this->getMorphClass())
                ->where('causer_id', $this->getKey())
                ->orderByDesc('id')
                ->limit(500)
                ->get(['id', 'log_name', 'description', 'event', 'subject_type', 'subject_id', 'properties', 'created_at'])
                ->map(fn (Activity $row) => [
                    'id' => $row->id,
                    'log_name' => $row->log_name,
                    'description' => $row->description,
                    'event' => $row->event,
                    'subject_type' => $row->subject_type,
                    'subject_id' => $row->subject_id,
                    'properties' => $row->properties,
                    'created_at' => optional($row->created_at)?->toIso8601String(),
                ])
                ->values()
                ->all();

            $personalDataSelection->add('activity.json', $activity);
        }

        if (! empty($this->avatar_path)) {
            $personalDataSelection->addFile($this->avatar_path, 'public', 'avatar');
        }
    }

    public function personalDataExportName(): string
    {
        $slug = Str::slug((string) ($this->name ?: $this->email ?: 'user'));

        return 'electrik-personal-data-'.$slug.'-'.$this->getKey();
    }
}
