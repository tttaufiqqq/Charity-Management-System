<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public', function (Blueprint $table) {
            $table->id('Public_ID');
            $table->foreignId('User_ID')->constrained('users')->onDelete('cascade');
            $table->string('Full_Name');
            $table->string('Phone', 20);
            $table->string('Email');
            $table->string('Position')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public');
    }
};
