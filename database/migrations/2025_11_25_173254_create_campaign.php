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
     * Creates the campaign table for fundraising campaigns.
     * Status values: 'Pending' (awaiting admin approval), 'Active', 'Completed'
     */
    public function up(): void
    {
        // Only run when migrating izzati database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'izzati') {
            return;
        }

        Schema::connection('izzati')->create('campaign', function (Blueprint $table) {
            $table->id('Campaign_ID');

            // ✅ Same database FK - KEEP (organization table is in izzati)
            $table->foreignId('Organization_ID')->constrained('organization', 'Organization_ID')->onDelete('cascade');

            $table->string('Title');
            $table->text('Description')->nullable();
            $table->decimal('Goal_Amount', 10, 2)->default(0); // Target fundraising amount
            $table->decimal('Collected_Amount', 10, 2)->default(0); // Current total donations
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->string('Status', 50)->default('Active'); // Pending, Active, Completed
            $table->timestamps();

            // Indexes for better query performance
            $table->index('Status'); // For filtering active/completed campaigns
            $table->index(['Start_Date', 'End_Date']); // For date range queries
            $table->index('created_at'); // For sorting by newest
        });
    }

    public function down(): void
    {
        // Only run when migrating izzati database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'izzati') {
            return;
        }

        Schema::connection('izzati')->dropIfExists('campaign');
    }
};
