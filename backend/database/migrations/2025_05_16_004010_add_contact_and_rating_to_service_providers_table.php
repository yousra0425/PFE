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
        Schema::table('service_providers', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('description');   // contact phone
            $table->string('email')->nullable()->after('phone');         // contact email
            $table->float('rating')->default(0)->after('longitude');     // average rating (optional)
        });
    }
    
    public function down()
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'rating']);
        });
    }
};
