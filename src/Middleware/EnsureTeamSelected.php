<?php

namespace Electrik\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If user has no teams, redirect to create team
        if ($user->teams()->count() === 0) {
            return redirect()->route('teams.create');
        }

        // If user has no current team selected, set the first team
        if (!$user->currentTeam) {
            $firstTeam = $user->teams()->first();
            if ($firstTeam) {
                $user->update(['current_team_id' => $firstTeam->id]);
                // Refresh the user model to ensure currentTeam relationship is loaded
                $user->refresh();
            }
        }

        return $next($request);
    }
}

