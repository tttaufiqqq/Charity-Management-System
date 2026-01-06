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
        Schema::connection('sashvini')->create('volunteer', function (Blueprint $table) {
            $table->id('Volunteer_ID');
            // ⚠️ Cross-database reference: User_ID references users table in izzhilmy database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('User_ID')->index();
            $table->string('Availability');
            $table->text('Address');
            $table->string('City', 100);
            $table->string('State', 100);
            $table->enum('Gender', ['Male', 'Female', 'Other']);
            $table->string('Phone_Num', 20);
            $table->text('Description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('sashvini')->dropIfExists('volunteer');
    }
};
