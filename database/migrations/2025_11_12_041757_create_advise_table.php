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
        Schema::create('advise', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('lecture_id');
            $table->uuid('student_user_id');
            $table->uuid('lecture_user_id');

            $table->enum('status', ['done', 'signed', 'pending'])->default('pending');
            $table->enum('type', ['khs', 'non-khs'])->default('non-khs');

            $table->string('khs')->nullable();
            $table->double('ipk')->nullable();
            $table->text('keluhan')->nullable();
            $table->text('masukan')->nullable();

            $table->uuid('semester_id')->nullable();
            $table->unsignedTinyInteger('semester')->nullable();
            $table->uuid('session_id')->nullable();

            $table->timestamps();

            // Foreign key relations to users
            $table->foreign('student_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lecture_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advise');
    }
};
