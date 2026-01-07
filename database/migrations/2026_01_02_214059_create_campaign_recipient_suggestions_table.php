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
     */
    public function up(): void
    {
        // Only run when migrating izzati database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'izzati') {
            return;
        }

        Schema::connection('izzati')->create('campaign_recipient_suggestions', function (Blueprint $table) {
            $table->id();

            // ✅ Same database FK - KEEP (campaign table is in izzati)
            $table->foreignId('Campaign_ID')->constrained('campaign', 'Campaign_ID')->onDelete('cascade');

            // ⚠️ Cross-database reference: Recipient_ID references recipient table in adam database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Recipient_ID')->index();

            // ⚠️ Cross-database reference: Suggested_By references users table in izzhilmy database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Suggested_By')->index(); // Admin user ID

            $table->text('Suggestion_Reason')->nullable();
            $table->enum('Status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->timestamps();

            // Unique constraint to prevent duplicate suggestions
            $table->unique(['Campaign_ID', 'Recipient_ID']);
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

        Schema::connection('izzati')->dropIfExists('campaign_recipient_suggestions');
    }
};
