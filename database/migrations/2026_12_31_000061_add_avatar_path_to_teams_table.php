<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teamsTable = config('teamwork.teams_table', 'teams');

        if (! Schema::hasTable($teamsTable)) {
            return;
        }

        Schema::table($teamsTable, function (Blueprint $table) use ($teamsTable) {
            if (! Schema::hasColumn($teamsTable, 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        $teamsTable = config('teamwork.teams_table', 'teams');

        if (! Schema::hasTable($teamsTable) || ! Schema::hasColumn($teamsTable, 'avatar_path')) {
            return;
        }

        Schema::table($teamsTable, function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};
