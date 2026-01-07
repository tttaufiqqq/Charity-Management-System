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
     * Creates the donor table for users with 'donor' role.
     * Tracks total donation amounts for each donor.
     */
    public function up(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->create('donor', function (Blueprint $table) {
            $table->id('Donor_ID');

            // ⚠️ Cross-database reference: User_ID references users table in izzhilmy database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('User_ID')->index();

            $table->string('Full_Name');
            $table->string('Phone_Num', 20);
            $table->decimal('Total_Donated', 10, 2)->default(0); // Cumulative donation total
            $table->timestamps();

            // Index for better query performance
            $table->index('Total_Donated'); // For sorting top donors
        });
    }

    public function down(): void
    {
        // Only run when migrating hannah database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'hannah') {
            return;
        }

        Schema::connection('hannah')->dropIfExists('donor');
    }
};
