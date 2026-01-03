<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_recipient_suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Campaign_ID');
            $table->unsignedBigInteger('Recipient_ID');
            $table->unsignedBigInteger('Suggested_By'); // Admin user ID
            $table->text('Suggestion_Reason')->nullable();
            $table->enum('Status', ['Pending', 'Accepted', 'Rejected'])->default('Pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('Campaign_ID')->references('Campaign_ID')->on('campaign')->onDelete('cascade');
            $table->foreign('Recipient_ID')->references('Recipient_ID')->on('recipient')->onDelete('cascade');
            $table->foreign('Suggested_By')->references('id')->on('users')->onDelete('cascade');

            // Unique constraint to prevent duplicate suggestions
            $table->unique(['Campaign_ID', 'Recipient_ID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_recipient_suggestions');
    }
};
