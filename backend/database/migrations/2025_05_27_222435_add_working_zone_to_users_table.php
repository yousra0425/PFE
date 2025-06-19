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
    Schema::table('users', function (Blueprint $table) {
        $table->decimal('working_latitude', 10, 7)->nullable();
        $table->decimal('working_longitude', 10, 7)->nullable();
        $table->integer('working_radius')->nullable(); // radius in meters
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['working_latitude', 'working_longitude', 'working_radius']);
    });
}

};
