<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the organization table for charity organizations/NGOs.
     * Each organization is managed by a user with the 'organizer' role.
     */
    public function up(): void
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->id('Organization_ID');

            // Cross-service reference - NO foreign key constraint
            $table->unsignedBigInteger('Organizer_ID');  // References users (in User Service DB)

            $table->string('Phone_No', 20);
            $table->string('Register_No', 50)->unique(); // Government registration number
            $table->text('Address');
            $table->string('State', 100);
            $table->string('City', 100);
            $table->text('Description')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('Organizer_ID');
            $table->index(['State', 'City']); // For location-based searches
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization');
    }
};
