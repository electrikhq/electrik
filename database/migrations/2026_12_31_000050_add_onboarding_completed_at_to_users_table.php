<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('email_verified_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'onboarding_completed_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed_at');
        });
    }
};
