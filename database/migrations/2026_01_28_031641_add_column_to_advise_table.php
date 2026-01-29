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
            $table->string('study_program')->after('semester')->nullable();
            $table->string('major')->after('study_program')->nullable();
            $table->string('tahun_akademik')->after('major')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advise', function (Blueprint $table) {
           $table->dropColumn('study_program');
           $table->dropColumn('major');
           $table->dropColumn('tahun_akademik');
        });
    }
};
