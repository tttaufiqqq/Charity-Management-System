<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donation', function (Blueprint $table) {
            $table->string('Payment_Status')->default('Pending')->after('Payment_Method');
            $table->string('Bill_Code')->nullable()->after('Payment_Status');
            $table->string('Transaction_ID')->nullable()->after('Bill_Code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation', function (Blueprint $table) {
            $table->dropColumn(['Payment_Status', 'Bill_Code', 'Transaction_ID']);
        });
    }
};
