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
            if (! Schema::hasColumn($teamsTable, 'brand_logo_path')) {
                $table->string('brand_logo_path')->nullable()->after('avatar_path');
            }

            if (! Schema::hasColumn($teamsTable, 'brand_primary')) {
                $table->string('brand_primary')->nullable()->after('brand_logo_path');
            }
        });
    }

    public function down(): void
    {
        $teamsTable = config('teamwork.teams_table', 'teams');

        if (! Schema::hasTable($teamsTable)) {
            return;
        }

        Schema::table($teamsTable, function (Blueprint $table) use ($teamsTable) {
            if (Schema::hasColumn($teamsTable, 'brand_primary')) {
                $table->dropColumn('brand_primary');
            }

            if (Schema::hasColumn($teamsTable, 'brand_logo_path')) {
                $table->dropColumn('brand_logo_path');
            }
        });
    }
};
