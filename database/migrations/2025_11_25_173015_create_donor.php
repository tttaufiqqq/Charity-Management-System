<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donor', function (Blueprint $table) {
            $table->id('Donor_ID');
            $table->foreignId('User_ID')->constrained('users')->onDelete('cascade');
            $table->string('Full_Name');
            $table->string('Phone_Num', 20);
            $table->decimal('Total_Donated', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor');
    }
};
