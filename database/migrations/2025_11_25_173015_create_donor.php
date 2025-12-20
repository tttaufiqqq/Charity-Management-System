<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the donor table for users with 'donor' role.
     * Tracks total donation amounts for each donor.
     */
    public function up(): void
    {
        Schema::create('donor', function (Blueprint $table) {
            $table->id('Donor_ID');
            $table->foreignId('User_ID')->constrained('users')->onDelete('cascade');
            $table->string('Full_Name');
            $table->string('Phone_Num', 20);
            $table->decimal('Total_Donated', 10, 2)->default(0); // Cumulative donation total
            $table->timestamps();

            // Indexes for better query performance
            $table->index('User_ID');
            $table->index('Total_Donated'); // For sorting top donors
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donor');
    }
};
