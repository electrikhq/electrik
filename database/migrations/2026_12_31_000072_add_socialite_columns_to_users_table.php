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

        $hasProvider = Schema::hasColumn('users', 'provider');
        $hasProviderId = Schema::hasColumn('users', 'provider_id');

        Schema::table('users', function (Blueprint $table) use ($hasProvider, $hasProviderId) {
            if (! $hasProvider) {
                $table->string('provider')->nullable()->after('password');
            }
            if (! $hasProviderId) {
                $table->string('provider_id')->nullable()->after('provider');
            }
            if (! $hasProvider && ! $hasProviderId) {
                $table->index(['provider', 'provider_id']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['provider_id', 'provider'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
