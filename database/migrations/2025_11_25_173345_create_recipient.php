<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipient', function (Blueprint $table) {
            $table->id('Recipient_ID');
            $table->foreignId('Public_ID')->nullable()->constrained('public', 'Public_ID')->onDelete('set null');
            $table->string('Name');
            $table->text('Address')->nullable();
            $table->string('Contact', 20)->nullable();
            $table->text('Need_Description')->nullable();
            $table->string('Status', 50)->default('Pending');
            $table->decimal('Approved_At', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipient');
    }
};
