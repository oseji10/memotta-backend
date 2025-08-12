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
            Schema::create('cohort_courses', function (Blueprint $table) {
            $table->id('cohortCourseId');
            $table->unsignedBigInteger('courseId')->nullable();
            $table->unsignedBigInteger('cohortId')->nullable();
            $table->string('startDate')->nullable();
            $table->string('endDate')->nullable();
            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            $table->foreign('cohortId')->references('cohortId')->on('cohorts')->onDelete('cascade');
            
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
