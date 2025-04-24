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
        Schema::table('cafe_configs', function (Blueprint $table) {
            $table->text('access_token')->change();
            $table->timestamp('token_refreshed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafe_configs', function (Blueprint $table) {
            $table->string('access_token')->change(); // Revert the column type back to string
            $table->dropColumn('token_refreshed_at'); // Drop the added column
        });
    }
};
