<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cashier customer columns on teams (Teamwork owns the teams table).
     */
    public function up(): void
    {
        if (! Schema::hasTable('teams')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table) {
            if (! Schema::hasColumn('teams', 'stripe_id')) {
                $table->string('stripe_id')->nullable()->index();
            }
            if (! Schema::hasColumn('teams', 'pm_type')) {
                $table->string('pm_type')->nullable();
            }
            if (! Schema::hasColumn('teams', 'pm_last_four')) {
                $table->string('pm_last_four', 4)->nullable();
            }
            if (! Schema::hasColumn('teams', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $cols = array_filter(['stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'], fn ($c) => Schema::hasColumn('teams', $c));
            if ($cols !== []) {
                $table->dropColumn($cols);
            }
        });
    }
};
