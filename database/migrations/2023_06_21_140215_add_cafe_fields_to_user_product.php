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
        Schema::table('user_product', function (Blueprint $table) {
            $table->string('purchase_token')->nullable();
            $table->string('gateway')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_product', function (Blueprint $table) {
            $table->dropColumn(['purchase_token', 'gateway']); // Drop the added columns
        });
    }
};
