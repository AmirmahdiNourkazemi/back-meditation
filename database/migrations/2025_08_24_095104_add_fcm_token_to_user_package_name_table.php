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
    Schema::table('user_package_name', function (Blueprint $table) {
        $table->string('fcm_token')->nullable();
    });
}

public function down()
{
    Schema::table('user_package_name', function (Blueprint $table) {
        $table->dropColumn('fcm_token');
    });
}

};
