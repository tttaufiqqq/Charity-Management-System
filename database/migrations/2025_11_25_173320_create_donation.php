<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation', function (Blueprint $table) {
            $table->id('Donation_ID');
            $table->foreignId('Donor_ID')->constrained('donor', 'Donor_ID')->onDelete('cascade');
            $table->foreignId('Campaign_ID')->constrained('campaign', 'Campaign_ID')->onDelete('cascade');
            $table->decimal('Amount', 10, 2);
            $table->date('Donation_Date');
            $table->string('Payment_Method', 50);
            $table->string('Receipt_No', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation');
    }
};
