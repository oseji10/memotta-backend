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
        Schema::create('cohort_students', function (Blueprint $table) {
            $table->id('cohortStudentId');
            $table->string('studentId')->nullable();
            $table->unsignedBigInteger('cohortId')->nullable();
            $table->unsignedBigInteger('courseId')->nullable();
            $table->unsignedBigInteger('userId')->nullable();


            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('cohortId')->references('cohortId')->on('cohorts')->onDelete('cascade');
            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

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
