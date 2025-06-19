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
            $table->float('working_radius')->nullable()->after('longitude');
        });
    }
    
    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('working_radius');
        });
    }
    
};
