<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_allocation', function (Blueprint $table) {
            // Cross-service references - NO foreign key constraints
            $table->unsignedBigInteger('Recipient_ID');  // References recipient (in Recipient Service DB)
            $table->unsignedBigInteger('Campaign_ID');   // References campaign (in Event Management DB)

            $table->decimal('Amount_Allocated', 10, 2);
            $table->date('Allocated_At');
            $table->timestamps();

            $table->primary(['Recipient_ID', 'Campaign_ID']);

            // Indexes for better query performance
            $table->index('Recipient_ID');
            $table->index('Campaign_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_allocation');
    }
};
