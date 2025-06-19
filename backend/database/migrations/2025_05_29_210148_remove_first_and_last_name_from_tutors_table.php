<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('tutors', function (Blueprint $table) {
        $table->dropColumn(['first_name', 'last_name']);
    });
}

public function down()
{
    Schema::table('tutors', function (Blueprint $table) {
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
    });
}

};
