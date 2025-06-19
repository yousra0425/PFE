<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id'); // who made the booking
            $table->unsignedBigInteger('tutor_id');   // tutor being booked
            
            $table->date('date');
            $table->time('time');
            $table->integer('duration')->default(1); // duration in hours
            
            $table->text('message')->nullable();
            
            $table->string('status')->default('pending'); // e.g., pending, confirmed, cancelled
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tutor_id')->references('id')->on('users')->onDelete('cascade');
            
            // Optional: Add an index for faster lookups by tutor or student
            $table->index('tutor_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
