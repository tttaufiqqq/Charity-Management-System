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
     * Creates the event_role table to define specific volunteer roles for each event.
     */
    public function up(): void
    {
        // Only run when migrating izzati database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'izzati') {
            return;
        }

        Schema::connection('izzati')->create('event_role', function (Blueprint $table) {
            $table->id('Role_ID');

            // ✅ Same database FK - KEEP (event table is in izzati)
            $table->foreignId('Event_ID')->constrained('event', 'Event_ID')->onDelete('cascade');
            $table->string('Role_Name'); // e.g., "Food Distributor", "Setup Crew", "Registration Desk"
            $table->text('Role_Description')->nullable();
            $table->integer('Volunteers_Needed'); // Number of volunteers needed for this role
            $table->integer('Volunteers_Filled')->default(0); // Number of volunteers currently assigned
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run when migrating izzati database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'izzati') {
            return;
        }

        Schema::connection('izzati')->dropIfExists('event_role');
    }
};
