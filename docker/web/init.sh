#!/bin/bash
set -e

export DB_NAME=${DB_NAME}
export DB_DATABASE_TEST=${DB_DATABASE_TEST}
export DB_USERNAME=${DB_USERNAME}
export DB_PASSWORD=${DB_PASSWORD}

# Copy .env.example to .env if it does not exist
ENV_FILE="/var/www/html/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo "📄 .env file does not exist. Copying .env.example to .env... 🚀"
    cp /var/www/html/.env.example "$ENV_FILE"
else
    echo "✅ .env file already exists. Skipping copy."
fi

VENDOR_DIR="/var/www/html/vendor"

if [ ! -d "$VENDOR_DIR" ] || [ -z "$(ls -A "$VENDOR_DIR" 2>/dev/null)" ]; then
    echo "📂 The 'vendor' directory is empty or does not exist. Installing dependencies with Composer... 🚀"
    composer install --working-dir=/var/www/html
else
    echo "✅ The 'vendor' directory already contains files. No need to install dependencies."
fi

chown -R www-data:www-data /var/www/html/storage/framework/views
chmod -R 775 /var/www/html/storage/framework/views
chown -R www-data:www-data /var/www/html/storage/logs
chmod -R 775 /var/www/html/storage/logs

# Copy .env.example to .env if it does not exist
ENV_FILE="/var/www/html/.env"
if [ ! -f "$ENV_FILE" ]; then
    echo "📄 .env file does not exist. Copying .env.example to .env... 🚀"
    cp /var/www/html/.env.example "$ENV_FILE"
else
    echo "✅ .env file already exists. Skipping copy."
fi

# ✅ Run supervisord (including PostgreSQL, PHP-FPM, and Nginx)
echo "🔧 Starting supervisord..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &

# ⏳ Wait until PostgreSQL is up (max 10 attempts)
echo "⏳ Waiting for PostgreSQL to be ready..."
ATTEMPTS=0
until pg_isready -h 127.0.0.1 -p 5432 -U "$DB_USERNAME" || [ $ATTEMPTS -eq 10 ]; do
    echo "⏳ Waiting for PostgreSQL... attempt $((++ATTEMPTS))"
    sleep 2
done

if [ $ATTEMPTS -eq 10 ]; then
    echo "❌ PostgreSQL did not become available. Exiting."
    exit 1
fi

# 🛠️ Check/create DBs if not exist
if su - postgres -c "psql -lqt | cut -d '|' -f 1 | grep -qw \"$DB_NAME\""; then
    echo "✅ Database '$DB_NAME' already exists."
else
    echo "🚀 Creating user and databases..."
    su - postgres -c "psql -c \"CREATE USER $DB_USERNAME WITH PASSWORD '$DB_PASSWORD';\""
    su - postgres -c "psql -c \"CREATE DATABASE $DB_NAME OWNER $DB_USERNAME;\""
    su - postgres -c "psql -c \"CREATE DATABASE $DB_DATABASE_TEST OWNER $DB_USERNAME;\""
fi

# 🚀 Run Laravel migrations

echo "🚀 Running migrations..."
USER_COUNT=$(su - postgres -c "psql -d $DB_NAME -t -c \"SELECT COUNT(*) FROM users;\"" | xargs || echo "0")
if [[ -z "$USER_COUNT" || ! "$USER_COUNT" =~ ^[0-9]+$ ]]; then
    USER_COUNT=0
fi
echo "👤 Number of users in the database: $USER_COUNT"
if [ "$USER_COUNT" -eq 0 ]; then
    echo "📂 No users found in the database. Running migrations with seeders... 🚀"
    php /var/www/html/artisan migrate --force --seed
    chmod -R 777 /var/www/html/storage
else
    echo "✅ Users already exist in the database. Running migrations without seeders... 🚀"
    php /var/www/html/artisan migrate --force
fi

if [ ! -f storage/api-docs/api-docs.json ]; then
  echo "→ Generando documentación Swagger..."
  php artisan l5-swagger:generate
fi

# Keep the container alive
tail -f /dev/null
