<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: sashvini (MariaDB)
     */
    protected $connection = 'sashvini';

    public function up(): void
    {
        // Only run when migrating sashvini database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'sashvini') {
            return;
        }

        Schema::connection('sashvini')->create('skill', function (Blueprint $table) {
            $table->id('Skill_ID');
            $table->string('Skill_Name');
            $table->text('Description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Only run when migrating sashvini database
        if (($_ENV['MIGRATING_DATABASE'] ?? env('MIGRATING_DATABASE')) !== 'sashvini') {
            return;
        }

        Schema::connection('sashvini')->dropIfExists('skill');
    }
};
