<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: izzati (PostgreSQL)
     */
    protected $connection = 'izzati';

    /**
     * Run the migrations.
     * Creates the organization table for charity organizations/NGOs.
     * Each organization is managed by a user with the 'organizer' role.
     */
    public function up(): void
    {
        Schema::connection('izzati')->create('organization', function (Blueprint $table) {
            $table->id('Organization_ID');

            // ⚠️ Cross-database reference: Organizer_ID references users table in izzhilmy database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Organizer_ID')->index();

            $table->string('Phone_No', 20);
            $table->string('Register_No', 50)->unique(); // Government registration number
            $table->text('Address');
            $table->string('State', 100);
            $table->string('City', 100);
            $table->text('Description')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['State', 'City']); // For location-based searches
        });
    }

    public function down(): void
    {
        Schema::connection('izzati')->dropIfExists('organization');
    }
};
