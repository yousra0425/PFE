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
        Schema::table('subcategories', function (Blueprint $table) {
            $table->foreignId('teaching_level_id')->constrained()->onDelete('cascade')->after('category_id');
        });
    }
    
    public function down()
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropForeign(['teaching_level_id']);
            $table->dropColumn('teaching_level_id');
        });
    }
};
