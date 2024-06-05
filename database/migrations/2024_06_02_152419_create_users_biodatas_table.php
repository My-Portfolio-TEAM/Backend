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
        Schema::create('users_biodatas', function (Blueprint $table) {
            $table->id();
            $table->string('about')->nullable();
            $table->string('headline')->nullable();
            $table->string('role')->nullable();
            $table->json('skills')->nullable();
            $table->string('location')->nullable();
            $table->string('linkedIn')->nullable();
            $table->string('website')->nullable();
            $table->foreignId('user_id')->unique()->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_biodatas');
    }
};
