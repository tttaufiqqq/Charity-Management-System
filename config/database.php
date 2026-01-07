<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [
        /*
        |--------------------------------------------------------------------------
        | Heterogeneous Distributed Database Architecture
        |--------------------------------------------------------------------------
        | Charity-Izz uses a distributed database architecture across 5 databases
        | with CENTRALIZED INFRASTRUCTURE.
        |
        | 📊 DATABASE DOMAIN DISTRIBUTION:
        | ================================
        |
        | 🔵 IZZHILMY (PostgreSQL) - CENTRALIZED INFRASTRUCTURE + Authentication
        |    Domain Tables:
        |    - users, roles, permissions, role_has_permissions, model_has_roles, etc.
        |
        |    Infrastructure Tables (SHARED BY ALL DATABASES):
        |    - sessions           → All user sessions across all 5 databases
        |    - cache, cache_locks → All cache data across all 5 databases
        |    - jobs, job_batches  → All queue jobs across all 5 databases
        |    - failed_jobs        → All failed jobs across all 5 databases
        |
        | 🟢 SASHVINI (MariaDB) - Volunteer Domain
        |    - volunteer, skill, volunteer_skill, event_participation
        |    Uses Izzhilmy for: sessions, cache, queue
        |
        | 🟣 IZZATI (PostgreSQL) - Operations Domain
        |    - organization, event, campaign, event_role, campaign_recipient_suggestions
        |    Uses Izzhilmy for: sessions, cache, queue
        |
        | 🟡 HANNAH (MySQL) - Finance Domain
        |    - donor, donation, donation_allocation
        |    Uses Izzhilmy for: sessions, cache, queue
        |
        | 🔴 ADAM (MySQL) - Public/Recipients Domain
        |    - public, recipient
        |    Uses Izzhilmy for: sessions, cache, queue
        |
        | ⭐ KEY CONCEPT: Centralized Infrastructure
        | ==========================================
        | When you interact with ANY of the 5 databases, the infrastructure
        | (sessions, cache, queue) is ALWAYS stored in and retrieved from Izzhilmy.
        |
        | Example:
        | - User logs in → Session stored in Izzhilmy
        | - Volunteer creates event participation → Uses Izzhilmy session
        | - Campaign data cached → Cache stored in Izzhilmy
        | - Job dispatched for donation → Job stored in Izzhilmy queue
        |
        */

        // Connection 1: Izzhilmy (PostgreSQL) - CENTRALIZED INFRASTRUCTURE + Auth
        'izzhilmy' => [
            'driver' => 'pgsql',
            'host' => env('DB5_HOST', '127.0.0.1'),
            'port' => env('DB5_PORT', 5432),
            'database' => env('DB5_DATABASE', 'workshop'),
            'username' => env('DB5_USERNAME', 'postgres'),
            'password' => env('DB5_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        // Connection 2: Sashvini (MariaDB) - Volunteer Management
        'sashvini' => [
            'driver' => 'mariadb', // MariaDB is MySQL-compatible
            'host' => env('DB3_HOST', '127.0.0.1'),
            'port' => env('DB3_PORT', 3306),
            'database' => env('DB3_DATABASE', 'charityworkshop'),
            'username' => env('DB3_USERNAME', 'root'),
            'password' => env('DB3_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        // Connection 3: Izzati (PostgreSQL) - Campaign & Event Operations
        'izzati' => [
            'driver' => 'pgsql',
            'host' => env('DB4_HOST', '127.0.0.1'),
            'port' => env('DB4_PORT', 5432),
            'database' => env('DB4_DATABASE', 'charityworkshop'),
            'username' => env('DB4_USERNAME', 'postgres'),
            'password' => env('DB4_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        // Connection 4: Hannah (MySQL) - Financial Transactions
        'hannah' => [
            'driver' => 'mysql',
            'host' => env('DB1_HOST', '127.0.0.1'),
            'port' => env('DB1_PORT', 3306),
            'database' => env('DB1_DATABASE', 'charityworkshop'),
            'username' => env('DB1_USERNAME', 'root'),
            'password' => env('DB1_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        // Connection 5: Adam (MySQL) - Public & Recipient Data
        'adam' => [
            'driver' => 'mysql',
            'host' => env('DB2_HOST', '127.0.0.1'),
            'port' => env('DB2_PORT', 3306),
            'database' => env('DB2_DATABASE', 'charityworkshop'),
            'username' => env('DB2_USERNAME', 'root'),
            'password' => env('DB2_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-database-'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
            'max_retries' => env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
