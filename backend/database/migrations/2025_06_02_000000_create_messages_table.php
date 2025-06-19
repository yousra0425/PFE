<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            
            // The user who owns the conversation or sent the message
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // The tutor related to the message (assuming tutors are users)
            $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');
            
            $table->text('text');
            
            // Sender's name (can be the user or the tutor)
            $table->string('sender');
            
            // Status can be 'Unread', 'Read', 'Archived', default to 'Unread'
            $table->string('status')->default('Unread');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
