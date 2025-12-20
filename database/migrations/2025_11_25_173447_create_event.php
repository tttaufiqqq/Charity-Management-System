<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the event table for volunteer events.
     * Status values: 'Pending' (awaiting approval), 'Upcoming', 'Ongoing', 'Completed', 'Cancelled'
     */
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id('Event_ID');
            $table->foreignId('Organizer_ID')->constrained('organization', 'Organization_ID')->onDelete('cascade');
            $table->string('Title');
            $table->text('Description')->nullable();
            $table->text('Location')->nullable();
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->integer('Capacity')->nullable(); // Maximum number of volunteers
            $table->string('Status', 50)->default('Upcoming'); // Pending, Upcoming, Ongoing, Completed, Cancelled
            $table->timestamps();

            // Indexes for better query performance
            $table->index('Organizer_ID');
            $table->index('Status'); // For filtering by event status
            $table->index(['Start_Date', 'End_Date']); // For date range queries
            $table->index('Start_Date'); // For sorting upcoming events
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
