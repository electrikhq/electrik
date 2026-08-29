<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionList
{
    /**
     * @return Collection<int, object{id: string, ip_address: ?string, user_agent: ?string, last_activity: int, is_current: bool, label: ?string}>
     */
    public static function forUser(Authenticatable $user, ?string $currentSessionId = null): Collection
    {
        if (! Schema::hasTable('sessions')) {
            return collect();
        }

        $currentSessionId ??= session()->getId();
        $labels = static::labelsForUser($user);

        return collect(
            DB::table('sessions')
                ->where('user_id', $user->getAuthIdentifier())
                ->orderByDesc('last_activity')
                ->get()
        )->map(function ($row) use ($currentSessionId, $labels) {
            $row->is_current = $row->id === $currentSessionId;
            $row->label = $labels[$row->id] ?? null;

            return $row;
        });
    }

    /**
     * @return array<string, string>
     */
    public static function labelsForUser(Authenticatable $user): array
    {
        if (! Schema::hasTable('session_labels')) {
            return [];
        }

        return DB::table('session_labels')
            ->where('user_id', $user->getAuthIdentifier())
            ->pluck('label', 'session_id')
            ->all();
    }

    public static function setLabel(Authenticatable $user, string $sessionId, ?string $label): void
    {
        if (! Schema::hasTable('session_labels')) {
            return;
        }

        $owns = DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', $sessionId)
            ->exists();

        if (! $owns) {
            return;
        }

        $label = trim((string) $label);

        if ($label === '') {
            DB::table('session_labels')
                ->where('user_id', $user->getAuthIdentifier())
                ->where('session_id', $sessionId)
                ->delete();

            return;
        }

        DB::table('session_labels')->updateOrInsert(
            [
                'user_id' => $user->getAuthIdentifier(),
                'session_id' => $sessionId,
            ],
            [
                'label' => \Illuminate\Support\Str::limit($label, 120, ''),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public static function deleteForUser(Authenticatable $user, string $sessionId): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', $sessionId)
            ->delete();

        if (Schema::hasTable('session_labels')) {
            DB::table('session_labels')
                ->where('user_id', $user->getAuthIdentifier())
                ->where('session_id', $sessionId)
                ->delete();
        }
    }

    public static function deleteOthers(Authenticatable $user, ?string $currentSessionId = null): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        $currentSessionId ??= session()->getId();

        $ids = DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->pluck('id');

        DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->delete();

        if (Schema::hasTable('session_labels') && $ids->isNotEmpty()) {
            DB::table('session_labels')
                ->where('user_id', $user->getAuthIdentifier())
                ->whereIn('session_id', $ids)
                ->delete();
        }
    }
}
