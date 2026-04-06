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
        Schema::create('environments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        Schema::create('action_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('access_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('ai_tones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('company_config_types', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('label', 150);
            $table->string('data_type', 50);
            $table->timestamps();
        });

        Schema::create('company_environment_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('environment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('config_type_id')->constrained('company_config_types')->cascadeOnDelete();
            $table->json('value_json')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'environment_id', 'config_type_id'], 'company_env_config_unique');
        });

        Schema::create('company_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('environment_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('url');
            $table->string('secret', 120)->nullable();
            $table->json('events')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_webhooks');
        Schema::dropIfExists('company_environment_configs');
        Schema::dropIfExists('company_config_types');
        Schema::dropIfExists('ai_tones');
        Schema::dropIfExists('access_methods');
        Schema::dropIfExists('action_types');
        Schema::dropIfExists('environments');
    }
};
