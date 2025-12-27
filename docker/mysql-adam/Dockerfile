FROM mysql:8.0

# Set maintainer label
LABEL maintainer="Charity-Izz Development Team"
LABEL description="MySQL database for Adam node (Public & Recipients)"

# Set timezone
ENV TZ=UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Create custom MySQL configuration for distributed transactions
RUN echo "[mysqld]" > /etc/mysql/conf.d/distributed.cnf && \
    echo "max_connections = 200" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "innodb_buffer_pool_size = 512M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "innodb_log_file_size = 128M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "innodb_flush_log_at_trx_commit = 1" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "innodb_flush_method = O_DIRECT" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "innodb_file_per_table = 1" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "transaction_isolation = READ-COMMITTED" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "max_allowed_packet = 64M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "sort_buffer_size = 4M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "read_buffer_size = 2M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "read_rnd_buffer_size = 8M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "join_buffer_size = 4M" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "query_cache_type = 0" >> /etc/mysql/conf.d/distributed.cnf && \
    echo "query_cache_size = 0" >> /etc/mysql/conf.d/distributed.cnf

# Copy initialization scripts
COPY init/*.sql /docker-entrypoint-initdb.d/ 2>/dev/null || true

# Expose MySQL port
EXPOSE 3306

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
  CMD mysqladmin ping -h localhost -u ${MYSQL_USER} -p${MYSQL_PASSWORD} || exit 1
