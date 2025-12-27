<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer', function (Blueprint $table) {
            $table->id('Volunteer_ID');

            // Cross-service reference - NO foreign key constraint
            $table->unsignedBigInteger('User_ID');  // References users (in User Service DB)
            $table->index('User_ID');

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
        Schema::dropIfExists('volunteer');
    }
};
