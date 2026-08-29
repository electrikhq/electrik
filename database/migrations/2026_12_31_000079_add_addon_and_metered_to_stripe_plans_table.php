<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stripe_plans')) {
            return;
        }

        Schema::table('stripe_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('stripe_plans', 'is_addon')) {
                $table->boolean('is_addon')->default(false)->after('seat_billing');
            }

            if (! Schema::hasColumn('stripe_plans', 'metered')) {
                $table->boolean('metered')->default(false)->after('is_addon');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('stripe_plans')) {
            return;
        }

        Schema::table('stripe_plans', function (Blueprint $table) {
            if (Schema::hasColumn('stripe_plans', 'metered')) {
                $table->dropColumn('metered');
            }

            if (Schema::hasColumn('stripe_plans', 'is_addon')) {
                $table->dropColumn('is_addon');
            }
        });
    }
};
