<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: adam (MySQL)
     */
    protected $connection = 'adam';

    /**
     * Run the migrations.
     * Creates the recipient table for individuals/families receiving aid.
     * Recipients must be approved by admins before receiving fund allocations.
     * Status values: 'Pending' (awaiting approval), 'Approved', 'Rejected'
     */
    public function up(): void
    {
        // Only run when migrating adam database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'adam') {
            return;
        }

        Schema::connection('adam')->create('recipient', function (Blueprint $table) {
            $table->id('Recipient_ID');

            // ✅ Same database FK - KEEP (public table is in adam)
            $table->foreignId('Public_ID')->nullable()->constrained('public', 'Public_ID')->onDelete('set null');

            $table->string('Name');
            $table->text('Address')->nullable();
            $table->string('Contact', 20)->nullable();
            $table->text('Need_Description')->nullable(); // Description of need/situation
            $table->string('Status', 50)->default('Pending'); // Pending, Approved, Rejected
            $table->timestamp('Approved_At')->nullable(); // When admin approved (was incorrectly decimal)
            $table->timestamps();

            // Indexes for better query performance
            $table->index('Status'); // For filtering pending/approved recipients
            $table->index('Approved_At'); // For sorting recently approved
        });
    }

    public function down(): void
    {
        // Only run when migrating adam database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'adam') {
            return;
        }

        Schema::connection('adam')->dropIfExists('recipient');
    }
};
