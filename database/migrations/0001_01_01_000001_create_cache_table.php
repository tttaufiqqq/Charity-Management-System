<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database connection for this migration
     * Connection: izzhilmy (PostgreSQL)
     */
    protected $connection = 'izzhilmy';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('izzhilmy')->create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::connection('izzhilmy')->create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('izzhilmy')->dropIfExists('cache');
        Schema::connection('izzhilmy')->dropIfExists('cache_locks');
    }
};
