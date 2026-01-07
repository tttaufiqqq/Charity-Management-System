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

        Schema::connection('sashvini')->create('volunteer_skill', function (Blueprint $table) {
            // ✅ Same database FKs - KEEP (both skill and volunteer are in sashvini)
            $table->foreignId('Skill_ID')->constrained('skill', 'Skill_ID')->onDelete('cascade');
            $table->foreignId('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');
            $table->string('Skill_Level')->nullable();
            $table->timestamps();

            $table->primary(['Skill_ID', 'Volunteer_ID']);
        });
    }

    public function down(): void
    {
        // Only run when migrating sashvini database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'sashvini') {
            return;
        }

        Schema::connection('sashvini')->dropIfExists('volunteer_skill');
    }
};
