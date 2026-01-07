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

    public function up(): void
    {
        // Only run when migrating adam database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'adam') {
            return;
        }

        Schema::connection('adam')->create('public', function (Blueprint $table) {
            $table->id('Public_ID');

            // ⚠️ Cross-database reference: User_ID references users table in izzhilmy database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('User_ID')->index();

            $table->string('Full_Name');
            $table->string('Phone', 20);
            $table->string('Email');
            $table->string('Position')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Only run when migrating adam database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'adam') {
            return;
        }

        Schema::connection('adam')->dropIfExists('public');
    }
};
