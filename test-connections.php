<?php

/**
 * Test Database Connections
 *
 * This script tests connectivity to all 5 distributed databases.
 * Run with: php test-connections.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$connections = [
    'izz' => [
        'name' => 'izzhilmy (User DB)',
        'type' => 'PostgreSQL',
        'port' => '5432',
    ],
    'sashvini' => [
        'name' => 'sashvini (Volunteer DB)',
        'type' => 'MariaDB',
        'port' => '3307',
    ],
    'izati' => [
        'name' => 'izati (Event DB)',
        'type' => 'PostgreSQL',
        'port' => '5433',
    ],
    'hannah' => [
        'name' => 'hannah (Donation DB)',
        'type' => 'MySQL',
        'port' => '3306',
    ],
    'adam' => [
        'name' => 'adam (Recipient DB)',
        'type' => 'MySQL',
        'port' => '3308',
    ],
];

echo "========================================\n";
echo "  Database Connection Test\n";
echo "========================================\n\n";

$allConnected = true;
$results = [];

foreach ($connections as $conn => $info) {
    echo "Testing {$info['name']} ({$info['type']})...\n";

    try {
        $start = microtime(true);
        $pdo = DB::connection($conn)->getPdo();
        $latency = round((microtime(true) - $start) * 1000, 2);

        // Get database name from connection
        $config = config("database.connections.{$conn}");
        $host = $config['host'] ?? 'unknown';
        $database = $config['database'] ?? 'unknown';

        echo "  ✅ Connected to {$host}:{$info['port']}\n";
        echo "     Database: {$database}\n";
        echo "     Latency: {$latency}ms\n";

        // Try a simple query
        $result = DB::connection($conn)->select('SELECT 1 as test');
        if ($result) {
            echo "     Query test: PASSED\n";
        }

        $results[$conn] = [
            'status' => 'success',
            'host' => $host,
            'latency' => $latency,
        ];

    } catch (Exception $e) {
        echo "  ❌ Failed\n";
        echo "     Error: {$e->getMessage()}\n";

        $allConnected = false;
        $results[$conn] = [
            'status' => 'failed',
            'error' => $e->getMessage(),
        ];
    }

    echo "\n";
}

echo "========================================\n";
echo "  Summary\n";
echo "========================================\n\n";

$successCount = count(array_filter($results, fn($r) => $r['status'] === 'success'));
$totalCount = count($results);

echo "Connected: {$successCount}/{$totalCount}\n\n";

if ($allConnected) {
    echo "🎉 All database connections successful!\n";
    echo "\nYou can now:\n";
    echo "  1. Start the Laravel app: php artisan serve\n";
    echo "  2. Test cross-database queries\n";
    echo "  3. Verify data synchronization across team members\n";
} else {
    echo "⚠️  Some connections failed.\n";
    echo "\nTroubleshooting:\n";
    echo "  1. Verify team member's Docker container is running\n";
    echo "  2. Check if IP address in .env is correct\n";
    echo "  3. Verify firewall allows the port\n";
    echo "  4. Try pinging the remote host\n";
    echo "  5. Check if Docker container is exposing the port\n";
    echo "\nFailed connections:\n";

    foreach ($results as $conn => $result) {
        if ($result['status'] === 'failed') {
            $info = $connections[$conn];
            echo "  - {$info['name']}: {$result['error']}\n";
        }
    }
}

echo "\n";
