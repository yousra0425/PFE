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
            $table->boolean('available')->default(false)->after('is_verified');
            $table->integer('reviews')->default(0)->after('available');
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn(['available', 'reviews']);
        });
    }
};
