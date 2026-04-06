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
        Schema::create('company_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('company_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('company_dictionaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('word', 120);
            $table->text('definition');
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('company_type_id')->nullable()->after('phone')->constrained('company_types')->nullOnDelete();
            $table->string('logo_path')->nullable()->after('company_type_id');
            $table->text('short_description')->nullable()->after('logo_path');
            $table->timestamp('registration_completed_at')->nullable()->after('short_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_type_id');
            $table->dropColumn(['logo_path', 'short_description', 'registration_completed_at']);
        });

        Schema::dropIfExists('company_dictionaries');
        Schema::dropIfExists('company_invitations');
        Schema::dropIfExists('company_types');
    }
};
