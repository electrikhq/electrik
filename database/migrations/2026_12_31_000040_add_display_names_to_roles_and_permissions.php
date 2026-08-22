<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('roles') && ! Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('display_name')->nullable()->after('name');
            });
        }

        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (! Schema::hasColumn('permissions', 'display_name')) {
                    $table->string('display_name')->nullable()->after('name');
                }
                if (! Schema::hasColumn('permissions', 'category_name')) {
                    $table->string('category_name')->nullable()->after('id');
                }
                if (! Schema::hasColumn('permissions', 'category_description')) {
                    $table->string('category_description')->nullable()->after('category_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'display_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('display_name');
            });
        }

        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                foreach (['display_name', 'category_name', 'category_description'] as $col) {
                    if (Schema::hasColumn('permissions', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
