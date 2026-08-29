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
            if (! Schema::hasColumn($teamsTable, 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('brand_primary');
            }

            if (! Schema::hasColumn($teamsTable, 'allowed_ips')) {
                $table->json('allowed_ips')->nullable()->after('archived_at');
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
            if (Schema::hasColumn($teamsTable, 'allowed_ips')) {
                $table->dropColumn('allowed_ips');
            }

            if (Schema::hasColumn($teamsTable, 'archived_at')) {
                $table->dropColumn('archived_at');
            }
        });
    }
};
