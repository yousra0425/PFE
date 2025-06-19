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
            $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');

            // Assuming you have a subcategories table
            $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
            $table->dropColumn('subcategory_id');
        });
    }
};
