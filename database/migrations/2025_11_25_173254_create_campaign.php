<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign', function (Blueprint $table) {
            $table->id('Campaign_ID');
            $table->foreignId('Organization_ID')->constrained('organization', 'Organization_ID')->onDelete('cascade');
            $table->string('Title');
            $table->text('Description')->nullable();
            $table->decimal('Goal_Amount', 10, 2)->default(0);
            $table->decimal('Collected_Amount', 10, 2)->default(0);
            $table->date('Start_Date');
            $table->date('End_Date');
            $table->string('Status', 50)->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign');
    }
};
