<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutorsTable extends Migration
{
    public function up()
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();  // one tutor per user
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 8, 2);
            $table->integer('experience_years')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('cin_image');
            $table->boolean('is_verified')->default(false);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('rating', 3, 2)->nullable(); // average rating, e.g. 4.75
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutors');
    }
}
