<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participation', function (Blueprint $table) {
            // Local reference (same Volunteer Service DB)
            $table->unsignedBigInteger('Volunteer_ID');
            $table->foreign('Volunteer_ID')->references('Volunteer_ID')->on('volunteer')->onDelete('cascade');

            // Cross-service reference - NO foreign key constraint
            $table->unsignedBigInteger('Event_ID');  // References event (in Event Management DB)

            $table->string('Status', 50)->default('Registered');
            $table->integer('Total_Hours')->default(0);
            $table->timestamps();

            $table->primary(['Volunteer_ID', 'Event_ID']);

            // Index for cross-service reference
            $table->index('Event_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participation');
    }
};
