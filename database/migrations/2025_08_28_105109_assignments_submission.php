// database/migrations/xxxx_xx_xx_xxxxxx_create_assignment_submissions_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignmentId');
            $table->unsignedBigInteger('studentId');
            $table->string('filePath');
            $table->timestamp('submittedAt');
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('gradedAt')->nullable();
            $table->timestamps();
            
            $table->foreign('assignmentId')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('studentId')->references('id')->on('users')->onDelete('cascade');

            // Ensure a student can only submit once per assignment
            $table->unique(['assignmentId', 'studentId']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
};