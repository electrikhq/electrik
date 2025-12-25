<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
                }
            });
            
            // Drop existing unique constraint if it exists
            // Try common index names that Spatie Permission might use
            $indexNames = [
                'model_has_roles_role_id_model_id_model_type_unique',
                'model_has_roles_role_model_type_unique',
            ];
            
            foreach ($indexNames as $indexName) {
                try {
                    Schema::table('model_has_roles', function (Blueprint $table) use ($indexName) {
                        $table->dropUnique([$indexName]);
                    });
                    break; // If successful, stop trying
                } catch (\Exception $e) {
                    // Index doesn't exist with this name, try next
                    continue;
                }
            }
            
            // Also try dropping by column names (for auto-generated index names)
            try {
                Schema::table('model_has_roles', function (Blueprint $table) {
                    $table->dropUnique(['role_id', 'model_id', 'model_type']);
                });
            } catch (\Exception $e) {
                // Index doesn't exist, that's fine
            }
            
            // Add new unique constraint with team_id
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unique(['role_id', 'model_id', 'model_type', 'team_id'], 'model_has_roles_role_model_type_team_unique');
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

