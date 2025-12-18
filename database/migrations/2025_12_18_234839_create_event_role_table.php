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
        Schema::create('event_role', function (Blueprint $table) {
            $table->id('Role_ID');
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
        Schema::dropIfExists('event_role');
    }
};
