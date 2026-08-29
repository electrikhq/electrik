<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrades installs that already ran the pre-Spatie team_activity_logs migration
 * (same batch name as 000062 before it was rewritten).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('team_activity_logs')) {
            Schema::drop('team_activity_logs');
        }

        if (Schema::hasTable('activity_log')) {
            if (! Schema::hasColumn('activity_log', 'team_id')) {
                Schema::table('activity_log', function (Blueprint $table) {
                    $table->foreignId('team_id')
                        ->nullable()
                        ->after('id')
                        ->constrained(config('teamwork.teams_table', 'teams'))
                        ->nullOnDelete();
                    $table->index(['team_id', 'created_at']);
                });
            }

            return;
        }

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('team_id')
                ->nullable()
                ->constrained(config('teamwork.teams_table', 'teams'))
                ->nullOnDelete();
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->json('attribute_changes')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        // Keep activity_log; downgrade would lose Spatie history.
    }
};
