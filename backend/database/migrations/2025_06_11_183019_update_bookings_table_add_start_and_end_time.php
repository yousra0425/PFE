<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBookingsTableAddStartAndEndTime extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('time'); // drop old time column
            $table->time('start_time')->after('date');
            $table->time('end_time')->after('start_time');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
            $table->time('time')->after('date');
        });
    }
}
