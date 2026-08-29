<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('company')->nullable();
                $table->string('status', 32)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['team_id', 'status']);
            });
        }

        if (Schema::hasTable('projects') && ! Schema::hasColumn('projects', 'client_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('client_id')->nullable()->after('team_id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('status', 32)->default('todo');
                $table->string('priority', 16)->default('normal');
                $table->date('due_on')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['team_id', 'status']);
                $table->index(['project_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');

        if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'client_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropConstrainedForeignId('client_id');
            });
        }

        Schema::dropIfExists('clients');
    }
};
