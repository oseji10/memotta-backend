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
        Schema::create('student_modules', function (Blueprint $table) {
            $table->id('studentModuleId');
            $table->unsignedBigInteger('courseId')->nullable();
            $table->string('studentId')->nullable();
            $table->unsignedBigInteger('moduleId')->nullable();
           
            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('moduleId')->references('moduleId')->on('course_modules')->onDelete('cascade');

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
