<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participation', function (Blueprint $table) {
            $table->foreignId('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');
            $table->foreignId('Event_ID')->constrained('event', 'Event_ID')->onDelete('cascade');
            $table->string('Status', 50)->default('Registered');
            $table->integer('Total_Hours')->default(0);
            $table->timestamps();

            $table->primary(['Volunteer_ID', 'Event_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participation');
    }
};
