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
            $table->foreignId('Role_ID')->nullable()->after('Event_ID')->constrained('event_role', 'Role_ID')->onDelete('set null');
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
