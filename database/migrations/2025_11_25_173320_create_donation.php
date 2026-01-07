<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: hannah (MySQL)
     */
    protected $connection = 'hannah';

    /**
     * Run the migrations.
     * Creates the donation table to track all donations to campaigns.
     * Each donation generates a unique receipt number.
     * Payment methods: Online Banking, Credit/Debit Card, E-Wallet, Other
     */
    public function up(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->create('donation', function (Blueprint $table) {
            $table->id('Donation_ID');

            // ✅ Same database FK - KEEP (donor table is in hannah)
            $table->foreignId('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');

            // ⚠️ Cross-database reference: Campaign_ID references campaign table in izzati database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Campaign_ID')->index();
            $table->decimal('Amount', 10, 2); // Donation amount
            $table->date('Donation_Date');
            $table->string('Payment_Method', 50); // Payment method used (FPX Online Banking, etc.)
            $table->string('Receipt_No', 100)->unique(); // Unique receipt identifier

            // ToyyibPay integration fields
            $table->string('Payment_Status', 20); // Only 'Completed' or 'Failed' (no Pending)
            $table->string('Bill_Code', 100)->nullable(); // ToyyibPay bill code
            $table->string('Transaction_ID', 100)->nullable(); // ToyyibPay transaction ID

            $table->timestamps();

            // Indexes for better query performance
            $table->index('Donation_Date'); // For date-based reports
            $table->index('created_at'); // For sorting recent donations
            $table->index('Payment_Status'); // For payment status queries
            $table->index('Bill_Code'); // For ToyyibPay lookups
        });
    }

    public function down(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->dropIfExists('donation');
    }
};
