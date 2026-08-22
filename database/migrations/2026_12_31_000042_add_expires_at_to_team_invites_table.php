<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('team_invites')) {
            return;
        }

        Schema::table('team_invites', function (Blueprint $table) {
            if (! Schema::hasColumn('team_invites', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('deny_token');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('team_invites') && Schema::hasColumn('team_invites', 'expires_at')) {
            Schema::table('team_invites', function (Blueprint $table) {
                $table->dropColumn('expires_at');
            });
        }
    }
};
