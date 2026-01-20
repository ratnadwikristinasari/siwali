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
        Schema::table('advise', function (Blueprint $table) {
            $table->uuid('semester_id')->after('masukan')->nullable();
            $table->uuid('session_id')->after('semester_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advise', function (Blueprint $table) {
            $table->dropColumn('semester_id');
            $table->dropColumn('session_id');

        });
    }
};
