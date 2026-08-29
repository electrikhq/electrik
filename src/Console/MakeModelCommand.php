<?php

namespace Electrik\Console;

use Illuminate\Support\Str;

class MakeModelCommand extends MakeCommand
{
    protected $signature = 'electrik:make:model
                            {name : Model class name (e.g. Post)}
                            {--team : Include team_id foreign key and BelongsToTeam (default: true)}
                            {--no-team : Skip team scoping}
                            {--no-migration : Do not create a migration}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate an Eloquent model (team-scoped by default) and optional migration';

    public function handle(): int
    {
        $raw = (string) $this->argument('name');
        $root = $this->laravel->getNamespace().'Models';
        $class = $this->qualifyClass($raw, $root);
        $basename = $this->classBasename($class);
        $relativeNs = $this->relativeNamespace($class, $root);
        $namespace = $relativeNs === ''
            ? rtrim($root, '\\')
            : $root.'\\'.$relativeNs;

        $withTeam = ! $this->option('no-team');
        $classPath = $this->pathFromClass($class);

        $fillable = $withTeam
            ? "['team_id', 'name']"
            : "['name']";

        $stub = $withTeam ? 'model-team.stub' : 'model.stub';

        $ok = $this->writeStub($stub, $classPath, [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $basename,
            '{{ fillable }}' => $fillable,
            '{{ teamRelation }}' => '',
        ]);

        if (! $ok) {
            return self::FAILURE;
        }

        if (! $this->option('no-migration')) {
            $table = $this->tableName($basename);
            $stamp = now()->format('Y_m_d_His');
            $file = database_path("migrations/{$stamp}_create_{$table}_table.php");
            $migrationStub = $withTeam ? 'migration-team.stub' : 'migration.stub';

            $this->writeStub($migrationStub, $file, [
                '{{ table }}' => $table,
            ]);
        }

        return self::SUCCESS;
    }
}
