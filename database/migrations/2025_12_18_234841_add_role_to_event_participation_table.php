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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('sashvini')->table('event_participation', function (Blueprint $table) {
            // ⚠️ Cross-database reference: Role_ID references event_role table in izzati database
            // Cannot use foreign key constraint across databases
            $table->unsignedBigInteger('Role_ID')->nullable()->after('Event_ID')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sashvini')->table('event_participation', function (Blueprint $table) {
            $table->dropColumn('Role_ID');
        });
    }
};
