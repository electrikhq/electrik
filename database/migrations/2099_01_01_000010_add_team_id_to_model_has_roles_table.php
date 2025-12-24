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
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                if (!Schema::hasColumn('model_has_roles', 'team_id')) {
                    $table->foreignId('team_id')->nullable()->after('role_id')->constrained('teams')->cascadeOnDelete();
                    
                    // Update unique constraint to include team_id
                    $table->dropUnique(['role_id', 'model_id', 'model_type']);
                    $table->unique(['role_id', 'model_id', 'model_type', 'team_id'], 'model_has_roles_role_model_type_team_unique');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                if (Schema::hasColumn('model_has_roles', 'team_id')) {
                    // Drop the unique constraint with team_id
                    $table->dropUnique('model_has_roles_role_model_type_team_unique');
                    // Restore original unique constraint
                    $table->unique(['role_id', 'model_id', 'model_type']);
                    
                    $table->dropForeign(['team_id']);
                    $table->dropColumn('team_id');
                }
            });
        }
    }
};

