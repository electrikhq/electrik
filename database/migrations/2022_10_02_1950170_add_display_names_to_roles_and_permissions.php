<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

		Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
        });
		
        Schema::table('permissions', function (Blueprint $table) {
			$table->string('display_name')->nullable()->after('name');
            $table->string('category_name')->nullable()->after('id');
            $table->string('category_description')->nullable()->after('category_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('category_name');
            $table->dropColumn('category_description');
            $table->dropColumn('display_name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('display_name');
        });

    }
};
