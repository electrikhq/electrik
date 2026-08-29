<?php

namespace Electrik\Console;

use Illuminate\Support\Str;

class MakeResourceCommand extends MakeCommand
{
    protected $signature = 'electrik:make:resource
                            {name : Resource name (e.g. Post)}
                            {--team : Scope model to team_id (default)}
                            {--no-team : Skip team scoping}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate model, migration, Livewire index/show, and a policy stub (team-scoped by default)';

    public function handle(): int
    {
        $name = Str::studly((string) $this->argument('name'));
        $force = $this->option('force') ? ['--force' => true] : [];
        $team = $this->option('no-team') ? ['--no-team' => true] : [];

        $modelExit = $this->call('electrik:make:model', array_merge([
            'name' => $name,
        ], $force, $team));

        if ($modelExit !== self::SUCCESS) {
            return $modelExit;
        }

        foreach (['Index', 'Show'] as $suffix) {
            $exit = $this->call('electrik:make:livewire', array_merge([
                'name' => $name.'/'.$suffix,
            ], $force));

            if ($exit !== self::SUCCESS) {
                return $exit;
            }
        }

        $root = $this->laravel->getNamespace().'Policies';
        $policyClass = $root.'\\'.$name.'Policy';
        $policyPath = $this->pathFromClass($policyClass);
        $modelFqcn = $this->laravel->getNamespace().'Models\\'.$name;

        $ok = $this->writeStub('policy.stub', $policyPath, [
            '{{ namespace }}' => rtrim($root, '\\'),
            '{{ class }}' => $name.'Policy',
            '{{ model }}' => $modelFqcn,
            '{{ modelBasename }}' => $name,
            '{{ modelVar }}' => Str::camel($name),
        ]);

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
