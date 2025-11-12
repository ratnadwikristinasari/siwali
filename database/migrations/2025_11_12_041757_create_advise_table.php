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
            $table->id();
            $table->uuid('student_id');
            $table->uuid('lecture_id');

            $table->string('description')->nullable();
            $table->enum('status', ['done', 'pending'])->default('pending');
            $table->string('khs')->nullable();
            $table->double('ipk')->nullable();

            $table->timestamps();

             // Foreign key relations to users
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lecture_id')->references('id')->on('users')->onDelete('cascade');
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
