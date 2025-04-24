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
            $table->string('myket_access_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_names', function (Blueprint $table) {
            $table->dropColumn('myket_access_token'); // Drop the column added in the `up` method
        });
    }
};
