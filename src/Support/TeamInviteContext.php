<?php

namespace Electrik\Support;

use Illuminate\Support\Carbon;
use Mpociot\Teamwork\Facades\Teamwork;
use Mpociot\Teamwork\TeamInvite;

class TeamInviteContext
{
    public const SESSION_TOKEN = 'electrik.invite_token';

    public const SESSION_PENDING_ROLE = 'electrik.invite_pending_role';

    public static function setPendingRole(?string $role): void
    {
        if ($role === null || $role === '') {
            session()->forget(self::SESSION_PENDING_ROLE);

            return;
        }

        session()->put(self::SESSION_PENDING_ROLE, $role);
    }

    public static function pullPendingRole(): ?string
    {
        $role = session()->pull(self::SESSION_PENDING_ROLE);

        return is_string($role) && $role !== '' ? $role : null;
    }

    public static function stashToken(string $token): void
    {
        session()->put(self::SESSION_TOKEN, $token);
    }

    public static function peekToken(): ?string
    {
        $token = session(self::SESSION_TOKEN);

        return is_string($token) && $token !== '' ? $token : null;
    }

    public static function pullToken(): ?string
    {
        $token = session()->pull(self::SESSION_TOKEN);

        return is_string($token) && $token !== '' ? $token : null;
    }

    public static function findValidAcceptInvite(?string $token): ?TeamInvite
    {
        if (! $token) {
            return null;
        }

        $invite = Teamwork::getInviteFromAcceptToken($token);

        if (! $invite || static::isExpired($invite)) {
            return null;
        }

        return $invite;
    }

    public static function isExpired(TeamInvite $invite): bool
    {
        if (! isset($invite->expires_at) || $invite->expires_at === null) {
            return false;
        }

        return Carbon::parse($invite->expires_at)->isPast();
    }

    public static function acceptUrl(TeamInvite $invite): string
    {
        return route('teams.invitations.accept', $invite->accept_token);
    }

    public static function denyUrl(TeamInvite $invite): string
    {
        return route('teams.invitations.deny', $invite->deny_token);
    }
}
