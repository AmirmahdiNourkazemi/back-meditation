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
            $table->foreignId('cafe_config_id')->nullable()->constrained('cafe_configs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_names', function (Blueprint $table) {
            $table->dropForeign(['cafe_config_id']); // Drop the foreign key constraint
            $table->dropColumn('cafe_config_id'); // Drop the column
        });
    }
};
