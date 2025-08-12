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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('paymentId');
            $table->string('studentId')->nullable();
            $table->unsignedBigInteger('courseId')->nullable();
            $table->string('courseCost')->nullable();
            $table->string('amountPaid')->nullable();
            $table->string('transactionReference')->nullable();
            $table->string('paymentStatus')->nullable();
            $table->string('paymentMethod')->nullable();

            $table->unsignedBigInteger('userId')->nullable();

            $table->foreign('courseId')->references('courseId')->on('courses')->onDelete('cascade');
            $table->foreign('studentId')->references('studentId')->on('students')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
