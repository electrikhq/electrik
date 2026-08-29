<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('personal_access_tokens')) {
            return;
        }

        if (Schema::hasColumn('personal_access_tokens', 'team_id')) {
            return;
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('tokenable_id')->constrained()->nullOnDelete();
            $table->index('team_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('personal_access_tokens') || ! Schema::hasColumn('personal_access_tokens', 'team_id')) {
            return;
        }

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
