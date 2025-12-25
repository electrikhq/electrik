<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only modify if subscriptions table exists (Cashier migrations have been run)
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                // Check if user_id column exists and team_id doesn't
                if (Schema::hasColumn('subscriptions', 'user_id') && !Schema::hasColumn('subscriptions', 'team_id')) {
                    // Rename user_id to team_id
                    $table->renameColumn('user_id', 'team_id');
                } elseif (!Schema::hasColumn('subscriptions', 'team_id')) {
                    // If user_id doesn't exist, add team_id
                    $table->unsignedBigInteger('team_id')->nullable()->after('id');
                    $table->index('team_id');
                }
            });
        }

        // Modify subscription_items table if it exists
        if (Schema::hasTable('subscription_items')) {
            Schema::table('subscription_items', function (Blueprint $table) {
                // No changes needed - subscription_items references subscriptions table
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'team_id') && !Schema::hasColumn('subscriptions', 'user_id')) {
                    $table->renameColumn('team_id', 'user_id');
                }
            });
        }
    }
};

