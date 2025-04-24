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
        Schema::create('user_package_name', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_name_id')->constrained('package_names')->onDelete('CASCADE');
            $table->foreignId('user_id')->constrained('users')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_package_name');
    }
};
