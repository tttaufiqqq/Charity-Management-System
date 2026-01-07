<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: sashvini (MariaDB)
     */
    protected $connection = 'sashvini';

    public function up(): void
    {
        // Only run when migrating sashvini database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'sashvini') {
            return;
        }

        Schema::connection('sashvini')->create('event_participation', function (Blueprint $table) {
            // ✅ Same database FK - KEEP (volunteer table is in sashvini)
            $table->foreignId('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');

            // ⚠️ Cross-database reference: Event_ID references event table in izzati database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Event_ID')->index();

            $table->string('Status', 50)->default('Registered');
            $table->integer('Total_Hours')->default(0);
            $table->timestamps();

            $table->primary(['Volunteer_ID', 'Event_ID']);
        });
    }

    public function down(): void
    {
        // Only run when migrating sashvini database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'sashvini') {
            return;
        }

        Schema::connection('sashvini')->dropIfExists('event_participation');
    }
};
