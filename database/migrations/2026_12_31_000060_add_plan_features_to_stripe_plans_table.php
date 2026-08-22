<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stripe_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('stripe_plans', 'features')) {
                $table->json('features')->nullable()->after('interval_count');
            }
            if (! Schema::hasColumn('stripe_plans', 'max_seats')) {
                $table->unsignedInteger('max_seats')->nullable()->after('features');
            }
            if (! Schema::hasColumn('stripe_plans', 'seat_billing')) {
                $table->boolean('seat_billing')->default(false)->after('max_seats');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stripe_plans', function (Blueprint $table) {
            foreach (['seat_billing', 'max_seats', 'features'] as $column) {
                if (Schema::hasColumn('stripe_plans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
