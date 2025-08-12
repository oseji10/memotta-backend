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
            $table->string('instructor')->nullable();
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
