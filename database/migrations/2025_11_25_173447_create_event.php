<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id('Event_ID');
            $table->foreignId('Organizer_ID')->constrained('organization', 'Organization_ID')->onDelete('cascade');
            $table->string('Title');
            $table->text('Description')->nullable();
            $table->text('Location')->nullable();
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->integer('Capacity')->nullable();
            $table->string('Status', 50)->default('Upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
