FROM postgres:16-alpine

# Set maintainer label
LABEL maintainer="Charity-Izz Development Team"
LABEL description="PostgreSQL database for Izzhilmy node (Authentication & Authorization)"

# Install additional utilities
RUN apk add --no-cache \
    postgresql-contrib \
    curl

# Set timezone
ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configure PostgreSQL for distributed transactions
# Increase max_connections and shared_buffers for distributed workload
RUN echo "max_connections = 200" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "shared_buffers = 256MB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "effective_cache_size = 1GB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "maintenance_work_mem = 128MB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "checkpoint_completion_target = 0.9" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "wal_buffers = 16MB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "default_statistics_target = 100" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "random_page_cost = 1.1" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "effective_io_concurrency = 200" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "work_mem = 8MB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "min_wal_size = 1GB" >> /usr/local/share/postgresql/postgresql.conf.sample && \
    echo "max_wal_size = 4GB" >> /usr/local/share/postgresql/postgresql.conf.sample

# Copy initialization scripts
COPY init/*.sql /docker-entrypoint-initdb.d/ 2>/dev/null || true

# Expose PostgreSQL port
EXPOSE 5432

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
  CMD pg_isready -U ${POSTGRES_USER} -d ${POSTGRES_DB} || exit 1
