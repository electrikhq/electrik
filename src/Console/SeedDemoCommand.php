<?php

namespace Electrik\Console;

use Electrik\Models\Client;
use Electrik\Models\Project;
use Electrik\Models\Task;
use Electrik\Support\ActivityLogger;
use Electrik\Support\EnsuresTeamRoles;
use Electrik\Support\Onboarding;
use Electrik\Support\SampleStudio;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class SeedDemoCommand extends Command
{
    protected $signature = 'electrik:seed-demo
                            {--email=demo@electrik.dev : Primary demo owner email}
                            {--password=password : Password for all seeded demo users}
                            {--force : Reset passwords for existing demo users}';

    protected $description = 'Seed demo users, teams, Studio (clients/projects/tasks), and activity';

    public function handle(EnsuresTeamRoles $ensures): int
    {
        $userModel = config('auth.providers.users.model');

        if (! is_string($userModel) || ! class_exists($userModel)) {
            $this->components->error('User model not configured.');

            return self::FAILURE;
        }

        $password = (string) $this->option('password');
        $ownerEmail = (string) $this->option('email');

        $owner = $this->upsertUser($userModel, 'Demo Owner', $ownerEmail, $password);
        $admin = $this->upsertUser($userModel, 'Demo Admin', 'admin@electrik.dev', $password);
        $member = $this->upsertUser($userModel, 'Demo Member', 'member@electrik.dev', $password);

        if (! method_exists($owner, 'createOwnedTeam')) {
            $this->components->error('User model is missing UserHasTeams.');

            return self::FAILURE;
        }

        $demoTeam = $owner->teams()->where('name', 'Demo Team')->first()
            ?? $owner->teams()->first();

        if (! $demoTeam) {
            $demoTeam = $owner->createOwnedTeam(['name' => 'Demo Team'], true);
        } else {
            $demoTeam->forceFill(['name' => 'Demo Team', 'owner_id' => $owner->getKey()])->save();
        }

        $acmeTeam = $owner->teams()->where('name', 'Acme Team')->first();

        if (! $acmeTeam) {
            $acmeTeam = $owner->createOwnedTeam(['name' => 'Acme Team'], false);
        }

        $ensures->syncCatalog();
        $ensures->ensureForTeam($demoTeam);
        $ensures->ensureForTeam($acmeTeam);

        $this->attachWithRole($owner, $demoTeam, 'owner');
        $this->attachWithRole($admin, $demoTeam, 'admin');
        $this->attachWithRole($member, $demoTeam, 'member');
        $this->attachWithRole($owner, $acmeTeam, 'owner');

        if (method_exists($owner, 'switchTeam')) {
            $owner->switchTeam($demoTeam);
        }

        foreach ([$owner, $admin, $member] as $user) {
            Onboarding::markCompleted($user);
        }

        if ($demoTeam->activityLogs()->count() === 0) {
            ActivityLogger::log($demoTeam, 'team.updated', $owner, $demoTeam, ['seed' => true]);
            ActivityLogger::log($demoTeam, 'member.joined', $admin, $admin, ['name' => $admin->name, 'seed' => true]);
            ActivityLogger::log($demoTeam, 'member.joined', $member, $member, ['name' => $member->name, 'seed' => true]);
            ActivityLogger::log($demoTeam, 'member.role_changed', $owner, $admin, [
                'name' => $admin->name,
                'role' => 'admin',
                'seed' => true,
            ]);
        }

        $this->seedStudio($demoTeam, $owner, $admin, $member);

        $this->components->info('Demo workspace ready.');
        $this->line('  Owner:    '.$ownerEmail.' / '.$password.'  (Demo Team + Acme Team)');
        $this->line('  Admin:    admin@electrik.dev / '.$password.'  (Demo Team)');
        $this->line('  Member:   member@electrik.dev / '.$password.'  (Demo Team)');
        if (SampleStudio::enabled()) {
            $this->line('  Studio:   /dashboard · /clients · /projects  (sample micro-SaaS)');
        }
        $this->line('  Login:    '.url('/login'));

        return self::SUCCESS;
    }

    protected function seedStudio(mixed $team, mixed $owner, mixed $admin, mixed $member): void
    {
        if (! SampleStudio::enabled()) {
            return;
        }

        $teamId = $team->getKey();

        $clients = Client::withoutTeamScope()->where('team_id', $teamId)->orderBy('id')->get();
        $createdClients = false;

        if ($clients->isEmpty()) {
            $createdClients = true;
            $clientDefs = [
                [
                    'name' => 'Ava Chen',
                    'email' => 'ava@northwind.co',
                    'company' => 'Northwind Labs',
                    'status' => 'active',
                    'notes' => 'Series A SaaS. Prefers weekly Loom updates.',
                ],
                [
                    'name' => 'Marcus Reed',
                    'email' => 'marcus@harbor.agency',
                    'company' => 'Harbor Agency',
                    'status' => 'active',
                    'notes' => 'Retainer for three brand sites.',
                ],
                [
                    'name' => 'Sofia Alvarez',
                    'email' => 'sofia@lumen.health',
                    'company' => 'Lumen Health',
                    'status' => 'paused',
                    'notes' => 'Paused until Q4 compliance review.',
                ],
            ];

            foreach ($clientDefs as $def) {
                $client = Client::withoutTeamScope()->create([
                    'team_id' => $teamId,
                    'created_by' => $owner->getKey(),
                    ...$def,
                ]);
                ActivityLogger::log($team, 'client.created', $owner, $client, [
                    'name' => $client->name,
                    'seed' => true,
                ]);
                $clients->push($client);
            }
        }

        $projects = Project::withoutTeamScope()->where('team_id', $teamId)->orderBy('id')->get();

        // Upgrade thin Projects-only seed into full Studio when clients land for the first time.
        if ($createdClients && $projects->isNotEmpty() && ! Task::withoutTeamScope()->where('team_id', $teamId)->exists()) {
            Project::withoutTeamScope()->where('team_id', $teamId)->delete();
            $projects = collect();
        }

        if ($projects->isEmpty()) {
            $projectDefs = [
                [
                    'name' => 'Northwind onboarding',
                    'description' => 'Invite flow, roles, and billing handoff for their internal ops team.',
                    'status' => 'active',
                    'due_on' => now()->addDays(10)->toDateString(),
                    'client' => 0,
                    'created_by' => $owner->getKey(),
                ],
                [
                    'name' => 'Harbor brand sites',
                    'description' => 'Three marketing sites on a shared design system.',
                    'status' => 'active',
                    'due_on' => now()->addDays(21)->toDateString(),
                    'client' => 1,
                    'created_by' => $admin->getKey(),
                ],
                [
                    'name' => 'Lumen compliance pack',
                    'description' => 'Audit trail exports and IP allowlist for clinical staff.',
                    'status' => 'paused',
                    'due_on' => now()->addDays(45)->toDateString(),
                    'client' => 2,
                    'created_by' => $owner->getKey(),
                ],
                [
                    'name' => 'Internal ops polish',
                    'description' => 'Housekeeping: announcements, ops metrics, email templates.',
                    'status' => 'active',
                    'due_on' => now()->addDays(5)->toDateString(),
                    'client' => null,
                    'created_by' => $admin->getKey(),
                ],
                [
                    'name' => 'Launch notes archive',
                    'description' => 'Shipped checklist from the first customer workspace.',
                    'status' => 'done',
                    'due_on' => now()->subDays(4)->toDateString(),
                    'client' => 0,
                    'created_by' => $member->getKey(),
                ],
            ];

            foreach ($projectDefs as $def) {
                $clientIndex = $def['client'];
                unset($def['client']);
                $project = Project::withoutTeamScope()->create([
                    'team_id' => $teamId,
                    'client_id' => is_int($clientIndex) ? $clients[$clientIndex]?->id : null,
                    ...$def,
                ]);
                ActivityLogger::log($team, 'project.created', $owner, $project, [
                    'name' => $project->name,
                    'seed' => true,
                ]);
                $projects->push($project);
            }
        } else {
            foreach ($projects as $index => $project) {
                if ($project->client_id || $clients->isEmpty()) {
                    continue;
                }
                $project->forceFill([
                    'client_id' => $clients[$index % $clients->count()]->id,
                ])->save();
            }
        }

        $hasTasks = Task::withoutTeamScope()->where('team_id', $teamId)->exists();

        if ($hasTasks || $projects->isEmpty()) {
            return;
        }

        $taskDefs = [
            [$projects[0]->id ?? null, 'Wire invite emails', 'doing', 'high', $admin->getKey(), now()->subDays(1)->toDateString()],
            [$projects[0]->id ?? null, 'Map owner vs admin permissions', 'todo', 'normal', $member->getKey(), now()->addDays(2)->toDateString()],
            [$projects[0]->id ?? null, 'Record Loom walkthrough', 'todo', 'low', $owner->getKey(), now()->addDays(6)->toDateString()],
            [$projects[1]->id ?? null, 'Shared token set', 'done', 'normal', $admin->getKey(), now()->subDays(2)->toDateString()],
            [$projects[1]->id ?? null, 'Site A homepage', 'doing', 'high', $member->getKey(), now()->addDays(3)->toDateString()],
            [$projects[1]->id ?? null, 'Site B case studies', 'todo', 'normal', $admin->getKey(), now()->addDays(8)->toDateString()],
            [$projects[1]->id ?? null, 'Site C contact form', 'blocked', 'high', $member->getKey(), now()->subDays(2)->toDateString()],
            [$projects[2]->id ?? null, 'Export activity CSV', 'todo', 'normal', $owner->getKey(), now()->addDays(20)->toDateString()],
            [$projects[3]->id ?? null, 'Draft ops announcement', 'doing', 'normal', $admin->getKey(), now()->subDays(1)->toDateString()],
            [$projects[3]->id ?? null, 'Preview billing emails', 'todo', 'low', $member->getKey(), now()->addDays(1)->toDateString()],
            [$projects[4]->id ?? null, 'Archive launch checklist', 'done', 'low', $owner->getKey(), now()->subDays(5)->toDateString()],
        ];

        foreach ($taskDefs as [$projectId, $title, $status, $priority, $assigneeId, $dueOn]) {
            if (! $projectId) {
                continue;
            }

            $task = Task::withoutTeamScope()->create([
                'team_id' => $teamId,
                'project_id' => $projectId,
                'created_by' => $owner->getKey(),
                'assignee_id' => $assigneeId,
                'title' => $title,
                'status' => $status,
                'priority' => $priority,
                'due_on' => $dueOn,
                'completed_at' => $status === 'done' ? now()->subDay() : null,
            ]);

            ActivityLogger::log($team, 'task.created', $owner, $task, [
                'name' => $task->title,
                'seed' => true,
            ]);
        }
    }

    /**
     * @param  class-string  $userModel
     */
    protected function upsertUser(string $userModel, string $name, string $email, string $password): mixed
    {
        $user = $userModel::query()->where('email', $email)->first();

        if ($user && ! $this->option('force')) {
            $this->components->warn("User {$email} already exists. Use --force to reset the password.");

            return $user;
        }

        if (! $user) {
            return $userModel::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'email_verified_at' => now(),
            ]);
        }

        $user->forceFill([
            'name' => $name,
            'password' => $password,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }

    protected function attachWithRole(mixed $user, mixed $team, string $role): void
    {
        $onTeam = method_exists($team, 'users')
            ? $team->users()->whereKey($user->getKey())->exists()
            : false;

        if (method_exists($user, 'attachTeam') && ! $onTeam) {
            $user->attachTeam($team);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$role]);
        }
    }
}
