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
        Schema::table('saved_jobs', function (Blueprint $table) {
        $table->dropForeign(['job_id']); // drop old FK
        $table->foreign('job_id')->references('id')->on('fremaa_jobs')->onDelete('cascade'); // or your actual table
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_jobs', function (Blueprint $table) {
        $table->dropForeign(['job_id']);
        $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade'); // rollback to old
    });
    }
};
