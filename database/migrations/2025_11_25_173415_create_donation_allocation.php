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

    public function up(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->create('donation_allocation', function (Blueprint $table) {
            // ⚠️ Cross-database reference: Recipient_ID references recipient table in adam database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Recipient_ID')->index();

            // ⚠️ Cross-database reference: Campaign_ID references campaign table in izzati database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Campaign_ID')->index();

            $table->decimal('Amount_Allocated', 10, 2);
            $table->date('Allocated_At');
            $table->timestamps();

            $table->primary(['Recipient_ID', 'Campaign_ID']);
        });
    }

    public function down(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->dropIfExists('donation_allocation');
    }
};
