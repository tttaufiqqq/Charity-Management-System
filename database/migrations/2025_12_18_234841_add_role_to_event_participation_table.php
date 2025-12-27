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
        Schema::table('event_participation', function (Blueprint $table) {
            // Cross-service reference - NO foreign key constraint
            $table->unsignedBigInteger('Role_ID')->nullable()->after('Event_ID');  // References event_role (in Event Management DB)
            $table->index('Role_ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_participation', function (Blueprint $table) {
            $table->dropForeign(['Role_ID']);
            $table->dropColumn('Role_ID');
        });
    }
};
