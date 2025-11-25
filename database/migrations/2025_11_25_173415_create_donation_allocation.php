<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_allocation', function (Blueprint $table) {
            $table->foreignId('Recipient_ID')->constrained('recipient', 'Recipient_ID')->onDelete('cascade');
            $table->foreignId('Campaign_ID')->constrained('campaign', 'Campaign_ID')->onDelete('cascade');
            $table->decimal('Amount_Allocated', 10, 2);
            $table->date('Allocated_At');
            $table->timestamps();

            $table->primary(['Recipient_ID', 'Campaign_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_allocation');
    }
};
