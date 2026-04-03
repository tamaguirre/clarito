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
        Schema::table('resumes', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('token');
            $table->string('file_path')->nullable()->after('original_name');
            $table->string('mime_type', 100)->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
        });

        Schema::dropIfExists('uploads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'file_path', 'mime_type', 'file_size']);
        });

        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('token', 64)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
