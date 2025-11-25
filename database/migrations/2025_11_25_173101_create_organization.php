<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->id('Organization_ID');
            $table->foreignId('Organizer_ID')->constrained('users')->onDelete('cascade');
            $table->string('Phone_No', 20);
            $table->string('Register_No', 50)->unique();
            $table->text('Address');
            $table->string('State', 100);
            $table->string('City', 100);
            $table->text('Description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organization');
    }
};
