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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('courseId');
            $table->string('courseName')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('cost')->nullable();
            $table->string('duration')->nullable();
            $table->unsignedBigInteger('instructor')->nullable();
            $table->unsignedBigInteger('addedBy')->nullable();
            $table->string('status')->default('active');
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('instructor')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('addedBy')->references('id')->on('users')->onDelete('cascade');
        

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
