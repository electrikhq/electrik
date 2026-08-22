<?php

namespace Electrik\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SessionList
{
    /**
     * @return Collection<int, object{id: string, ip_address: ?string, user_agent: ?string, last_activity: int, is_current: bool}>
     */
    public static function forUser(Authenticatable $user, ?string $currentSessionId = null): Collection
    {
        if (! Schema::hasTable('sessions')) {
            return collect();
        }

        $currentSessionId ??= session()->getId();

        return collect(
            DB::table('sessions')
                ->where('user_id', $user->getAuthIdentifier())
                ->orderByDesc('last_activity')
                ->get()
        )->map(function ($row) use ($currentSessionId) {
            $row->is_current = $row->id === $currentSessionId;

            return $row;
        });
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
    }

    public static function deleteOthers(Authenticatable $user, ?string $currentSessionId = null): void
    {
        if (! Schema::hasTable('sessions')) {
            return;
        }

        $currentSessionId ??= session()->getId();

        DB::table('sessions')
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }
}
