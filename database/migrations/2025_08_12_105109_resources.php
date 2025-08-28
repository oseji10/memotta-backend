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
        Schema::create('resources', function (Blueprint $table) {
            $table->id('resourceId');
            $table->unsignedBigInteger('courseId')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('filePath')->nullable();
            $table->string('externalUrl')->nullable();
           
            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            
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
