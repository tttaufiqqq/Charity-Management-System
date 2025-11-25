<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_skill', function (Blueprint $table) {
            $table->foreignId('Skill_ID')->constrained('skill', 'Skill_ID')->onDelete('cascade');
            $table->foreignId('Volunteer_ID')->constrained('volunteer', 'Volunteer_ID')->onDelete('cascade');
            $table->string('Skill_Level')->nullable();
            $table->timestamps();

            $table->primary(['Skill_ID', 'Volunteer_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_skill');
    }
};

