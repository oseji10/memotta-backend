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
            Schema::create('documents', function (Blueprint $table) {
            $table->id('documentId');
            $table->string('studentId')->nullable();
            $table->string('documentType')->nullable();
            $table->string('url')->nullable();
           
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            
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
