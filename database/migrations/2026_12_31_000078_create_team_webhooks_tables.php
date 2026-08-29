<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('team_webhooks')) {
            return;
        }

        Schema::create('team_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('secret', 64)->nullable();
            $table->json('events')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->unsignedInteger('failure_count')->default(0);
            $table->timestamps();
        });

        if (! Schema::hasTable('team_webhook_deliveries')) {
            Schema::create('team_webhook_deliveries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_webhook_id')->constrained('team_webhooks')->cascadeOnDelete();
                $table->string('event');
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->boolean('successful')->default(false);
                $table->text('response_body')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_webhook_deliveries');
        Schema::dropIfExists('team_webhooks');
    }
};
