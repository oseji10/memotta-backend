// database/migrations/xxxx_xx_xx_xxxxxx_create_assignments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('filePath');
            $table->dateTime('dueDate');
            $table->integer('maxScore')->default(100);
            $table->unsignedBigInteger('courseId');
            $table->unsignedBigInteger('createdBy');

            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            $table->foreign('createdBy')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};