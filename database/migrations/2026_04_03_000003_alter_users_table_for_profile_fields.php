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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['education_level', 'conditions']);
            $table->foreignId('education_level_id')->nullable()->after('birth_date')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('education_level_id');
            $table->string('education_level')->nullable()->after('birth_date');
            $table->json('conditions')->nullable()->after('education_level');
        });
    }
};
