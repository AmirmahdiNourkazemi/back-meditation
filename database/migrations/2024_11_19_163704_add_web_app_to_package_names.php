<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_names', function (Blueprint $table) {
            $table->string('web_app_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_names', function (Blueprint $table) {
            $table->dropColumn('web_app_url'); // Drop the added column
        });
    }
};
