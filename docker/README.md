# Charity-Izz Distributed Database Docker Setup

This directory contains Docker configuration for the distributed database architecture across 5 heterogeneous database nodes.

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Charity-Izz Application                   │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Izzhilmy    │    │    Izati     │    │   Hannah     │
│  PostgreSQL  │    │  PostgreSQL  │    │    MySQL     │
│    :5432     │    │    :5433     │    │    :3306     │
│              │    │              │    │              │
│  - users     │    │ - organization│   │  - donor     │
│  - roles     │    │ - event      │    │  - donation  │
│              │    │ - campaign   │    │  - allocation│
└──────────────┘    │ - event_role │    └──────────────┘
                    └──────────────┘
        ┌───────────────────┼───────────────────┐
        ▼                   ▼
┌──────────────┐    ┌──────────────┐
│    Adam      │    │  Sashvini    │
│    MySQL     │    │   MariaDB    │
│    :3307     │    │    :3308     │
│              │    │              │
│  - public    │    │  - volunteer │
│  - recipient │    │  - skill     │
│              │    │  - participation
└──────────────┘    └──────────────┘
```

## Database Nodes

### 1. Izzhilmy (PostgreSQL 16)
- **Port**: 5432
- **Purpose**: Authentication & Authorization
- **Tables**: `users`, `roles`, `model_has_roles`, `permissions`, `sessions`, `cache`, `jobs`, `migrations`
- **Connection**: `izzhilmy` in `config/database.php`

### 2. Izati (PostgreSQL 16)
- **Port**: 5433
- **Purpose**: Organization & Events Management
- **Tables**: `organization`, `event`, `campaign`, `event_role`
- **Connection**: `izati` in `config/database.php`

### 3. Hannah (MySQL 8.0)
- **Port**: 3306
- **Purpose**: Donations & Fund Allocations
- **Tables**: `donor`, `donation`, `donation_allocation`
- **Connection**: `hannah` in `config/database.php`

### 4. Adam (MySQL 8.0)
- **Port**: 3307
- **Purpose**: Public Profiles & Recipients
- **Tables**: `public`, `recipient`
- **Connection**: `adam` in `config/database.php`

### 5. Sashvini (MariaDB 11.2)
- **Port**: 3308
- **Purpose**: Volunteer Management
- **Tables**: `volunteer`, `volunteer_skill`, `skill`, `event_participation`
- **Connection**: `sahsvini` in `config/database.php`

## Quick Start

### 1. Single Machine Setup (Development)

```bash
# Navigate to docker directory
cd docker

# Copy environment file
cp .env.docker .env

# Start all containers
docker-compose up -d

# Check container status
docker-compose ps

# View logs
docker-compose logs -f

# Stop all containers
docker-compose down

# Stop and remove volumes (WARNING: Deletes all data!)
docker-compose down -v
```

### 2. Multi-Machine Setup (Production)

For deploying across different physical machines:

1. **Edit `.env.docker`** on the Laravel application server:
   ```env
   # Replace container names with actual IP addresses
   DB5_HOST=192.168.1.10  # Izzhilmy server IP
   DB4_HOST=192.168.1.11  # Izati server IP
   DB1_HOST=192.168.1.12  # Hannah server IP
   DB2_HOST=192.168.1.13  # Adam server IP
   DB4_HOST=192.168.1.14  # Sashvini server IP
   ```

2. **On each database server**, run only the specific service:
   ```bash
   # On Izzhilmy server (192.168.1.10)
   docker-compose up -d postgres-izzhilmy

   # On Izati server (192.168.1.11)
   docker-compose up -d postgres-izati

   # On Hannah server (192.168.1.12)
   docker-compose up -d mysql-hannah

   # On Adam server (192.168.1.13)
   docker-compose up -d mysql-adam

   # On Sashvini server (192.168.1.14)
   docker-compose up -d mariadb-sashvini
   ```

3. **Configure firewalls** to allow traffic on database ports:
   ```bash
   # Example for Ubuntu/Debian
   sudo ufw allow 5432/tcp  # PostgreSQL
   sudo ufw allow 3306/tcp  # MySQL/MariaDB
   ```

## Database Initialization

### Automatic Initialization

Place SQL scripts in the `init/` directory of each database:

```
docker/
├── postgresql-izzhilmy/
│   └── init/
│       └── 01-create-extensions.sql
├── postgresql-izati/
│   └── init/
│       └── 01-create-extensions.sql
├── mysql-hannah/
│   └── init/
│       └── 01-create-database.sql
├── mysql-adam/
│   └── init/
│       └── 01-create-database.sql
└── mariadb-sashvini/
    └── init/
        └── 01-create-database.sql
```

Scripts are executed in alphabetical order on first container startup.

### Manual Initialization via Laravel

```bash
# Run distributed migrations (after implementing distributed migration command)
php artisan migrate:distributed --all

# Seed all databases
php artisan db:seed --database=izzhilmy
php artisan db:seed --database=izati
php artisan db:seed --database=hannah
php artisan db:seed --database=adam
php artisan db:seed --database=sahsvini
```

## Database Access

### Command Line Access

```bash
# PostgreSQL - Izzhilmy
docker exec -it charity-izz-postgres-izzhilmy psql -U izzhilmy -d charity_izzhilmy

# PostgreSQL - Izati
docker exec -it charity-izz-postgres-izati psql -U izati -d charity_izati

# MySQL - Hannah
docker exec -it charity-izz-mysql-hannah mysql -u hannah -p charity_hannah

# MySQL - Adam
docker exec -it charity-izz-mysql-adam mysql -u adam -p charity_adam

# MariaDB - Sashvini
docker exec -it charity-izz-mariadb-sashvini mysql -u sahsvini -p charity_sashvini
```

### GUI Tools

- **PostgreSQL**: pgAdmin, DBeaver, TablePlus
  - Izzhilmy: `localhost:5432`
  - Izati: `localhost:5433`

- **MySQL/MariaDB**: MySQL Workbench, DBeaver, TablePlus
  - Hannah: `localhost:3306`
  - Adam: `localhost:3307`
  - Sashvini: `localhost:3308`

## Health Checks

All containers have health checks configured:

```bash
# Check health status of all containers
docker-compose ps

# View health check logs
docker inspect --format='{{json .State.Health}}' charity-izz-postgres-izzhilmy | jq

# Manual health check
docker exec charity-izz-postgres-izzhilmy pg_isready -U izzhilmy
docker exec charity-izz-mysql-hannah mysqladmin ping -u hannah -phannah_password_2024
```

## Performance Tuning

### PostgreSQL (Izzhilmy & Izati)
- `max_connections`: 200
- `shared_buffers`: 256MB
- `effective_cache_size`: 1GB
- `work_mem`: 8MB

### MySQL/MariaDB (Hannah, Adam, Sashvini)
- `max_connections`: 200
- `innodb_buffer_pool_size`: 512MB
- `innodb_log_file_size`: 128MB
- `transaction_isolation`: READ-COMMITTED

Adjust these values in Dockerfiles based on available system resources.

## Backup & Restore

### PostgreSQL Backup

```bash
# Backup Izzhilmy
docker exec charity-izz-postgres-izzhilmy pg_dump -U izzhilmy charity_izzhilmy > backup_izzhilmy.sql

# Restore Izzhilmy
docker exec -i charity-izz-postgres-izzhilmy psql -U izzhilmy charity_izzhilmy < backup_izzhilmy.sql
```

### MySQL/MariaDB Backup

```bash
# Backup Hannah
docker exec charity-izz-mysql-hannah mysqldump -u hannah -phannah_password_2024 charity_hannah > backup_hannah.sql

# Restore Hannah
docker exec -i charity-izz-mysql-hannah mysql -u hannah -phannah_password_2024 charity_hannah < backup_hannah.sql
```

## Troubleshooting

### Container won't start
```bash
# Check logs
docker-compose logs [service-name]

# Example
docker-compose logs postgres-izzhilmy

# Check disk space
df -h

# Remove old volumes if needed
docker-compose down -v
docker volume prune
```

### Connection refused
```bash
# Verify container is running
docker-compose ps

# Check port bindings
docker port charity-izz-postgres-izzhilmy

# Test connection from host
psql -h localhost -p 5432 -U izzhilmy -d charity_izzhilmy
mysql -h localhost -P 3306 -u hannah -p
```

### Permission denied
```bash
# On Linux, may need to adjust volume permissions
sudo chown -R 999:999 postgres_izzhilmy_data
sudo chown -R 999:999 mysql_hannah_data
```

### Network issues between containers
```bash
# Inspect network
docker network inspect docker_charity-izz-network

# Test connectivity between containers
docker exec charity-izz-postgres-izzhilmy ping postgres-izati
```

## Environment Variables

All environment variables are defined in `.env.docker`:

| Variable | Description | Default |
|----------|-------------|---------|
| `DB5_HOST` | Izzhilmy PostgreSQL host | `postgres-izzhilmy` |
| `DB5_PORT` | Izzhilmy PostgreSQL port | `5432` |
| `DB5_DATABASE` | Izzhilmy database name | `charity_izzhilmy` |
| `DB5_USERNAME` | Izzhilmy username | `izzhilmy` |
| `DB5_PASSWORD` | Izzhilmy password | `izzhilmy_password_2024` |
| `DB4_HOST` | Izati PostgreSQL host | `postgres-izati` |
| `DB1_HOST` | Hannah MySQL host | `mysql-hannah` |
| `DB2_HOST` | Adam MySQL host | `mysql-adam` |
| `DB4_HOST` | Sashvini MariaDB host | `mariadb-sashvini` |

**Note**: Change all default passwords in production!

## Security Considerations

1. **Change default passwords** in `.env.docker`
2. **Use secrets management** for production (Docker Secrets, Vault)
3. **Enable SSL/TLS** for cross-machine communication
4. **Restrict network access** with firewall rules
5. **Regular backups** of all database nodes
6. **Monitor failed login attempts**
7. **Keep database versions updated**

## Next Steps

1. Run `docker-compose up -d` to start all containers
2. Update Laravel `.env` file with connection details
3. Run distributed migrations: `php artisan migrate:distributed --all`
4. Test database connectivity from Laravel application
5. Configure replication layer for cross-database sync
6. Implement distributed transaction manager

## Support

For issues related to:
- Docker configuration: Check logs with `docker-compose logs`
- Laravel connection: Verify `.env` database credentials
- Network issues: Inspect `docker network ls` and firewall rules
- Performance: Adjust configuration in Dockerfiles

## License

Part of the Charity-Izz project. See main project README for license information.
