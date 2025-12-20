<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the donation table to track all donations to campaigns.
     * Each donation generates a unique receipt number.
     * Payment methods: Online Banking, Credit/Debit Card, E-Wallet, Other
     */
    public function up(): void
    {
        Schema::create('donation', function (Blueprint $table) {
            $table->id('Donation_ID');
            $table->foreignId('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');
            $table->foreignId('Campaign_ID')->constrained('campaign', 'Campaign_ID')->onDelete('cascade');
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
            $table->index('Donor_ID'); // For donor's donation history
            $table->index('Campaign_ID'); // For campaign donation tracking
            $table->index('Donation_Date'); // For date-based reports
            $table->index('created_at'); // For sorting recent donations
            $table->index('Payment_Status'); // For payment status queries
            $table->index('Bill_Code'); // For ToyyibPay lookups
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation');
    }
};
