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
        Schema::create('package_names', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('title');
            $table->string('image')->nullable();
            $table->string('v1_identifier')->unique()->nullable();
            $table->uuid('uuid')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_names');
    }
};
