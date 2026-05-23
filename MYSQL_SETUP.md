# MySQL Database Configuration

## ✅ MySQL Successfully Configured

Your Laravel Error Dashboard is now using **MySQL** instead of SQLite.

---

## Database Details

- **Database Name**: `error_dashboard`
- **Username**: `root`
- **Password**: (blank)
- **Host**: `127.0.0.1`
- **Port**: `3306`
- **Connection**: `mysql`

---

## Configuration File

### `.env` Settings

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=error_dashboard
DB_USERNAME=root
DB_PASSWORD=
```

---

## Database Tables Created

The following Laravel tables have been migrated to MySQL:

✅ **migrations** - Migration tracking
✅ **users** - User accounts
✅ **password_reset_tokens** - Password resets
✅ **sessions** - User sessions
✅ **cache** - Application cache
✅ **cache_locks** - Cache locking
✅ **jobs** - Queue jobs
✅ **job_batches** - Batch jobs
✅ **failed_jobs** - Failed queue jobs

---

## Verification

### Check Database Connection

```bash
php artisan db:show
```

**Output:**
```
MySQL ......................................................... 8.0.30
Connection ..................................................... mysql
Database ............................................. error_dashboard
Host ....................................................... 127.0.0.1
Port ............................................................ 3306
Username ........................................................ root
```

### List All Tables

```bash
php artisan db:table --database=mysql
```

### Run Migrations

```bash
# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Regular migration (runs pending migrations)
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

---

## MySQL Commands

### Access MySQL CLI

```bash
mysql -u root -p error_dashboard
```

### Show Tables

```sql
SHOW TABLES;
```

### Describe Table Structure

```sql
DESCRIBE users;
DESCRIBE cache;
DESCRIBE sessions;
```

### Check Table Data

```sql
SELECT * FROM migrations;
SELECT * FROM users;
```

### Database Size

```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'error_dashboard'
GROUP BY table_schema;
```

---

## Cache Configuration

The application uses **database** as the cache driver. This means cache data is stored in the `cache` table in MySQL.

### Cache Commands

```bash
# Clear all cache
php artisan cache:clear

# View cache statistics
php artisan cache:table

# Flush cache
php artisan cache:forget key_name
```

---

## Session Configuration

Sessions are stored in the **database** (`sessions` table).

### Session Commands

```bash
# Clear expired sessions
php artisan session:table
```

---

## Queue Configuration

Queue jobs are stored in the **database** (`jobs` table).

### Queue Commands

```bash
# Process queue jobs
php artisan queue:work

# List failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Clear all failed jobs
php artisan queue:flush
```

---

## Backup & Restore

### Backup Database

```bash
# Using mysqldump
mysqldump -u root error_dashboard > backup.sql

# With timestamp
mysqldump -u root error_dashboard > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database

```bash
mysql -u root error_dashboard < backup.sql
```

---

## Performance Optimization

### Add Indexes (if needed)

```sql
-- Example: Add index to cache table
ALTER TABLE cache ADD INDEX idx_key (key);

-- Example: Add index to sessions table
ALTER TABLE sessions ADD INDEX idx_last_activity (last_activity);
```

### Optimize Tables

```bash
# Via Artisan
php artisan db:optimize

# Via MySQL
mysql -u root -e "OPTIMIZE TABLE error_dashboard.cache, error_dashboard.sessions;"
```

---

## Troubleshooting

### Connection Issues

**Problem**: Can't connect to MySQL

**Solutions**:
1. Check MySQL is running:
   ```bash
   mysql -u root -e "SELECT 1"
   ```

2. Verify credentials in `.env`

3. Clear config cache:
   ```bash
   php artisan config:clear
   ```

4. Test connection:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

### Migration Errors

**Problem**: Migration fails

**Solutions**:
1. Check database exists:
   ```bash
   mysql -u root -e "SHOW DATABASES LIKE 'error_dashboard';"
   ```

2. Drop and recreate:
   ```bash
   mysql -u root -e "DROP DATABASE IF EXISTS error_dashboard; CREATE DATABASE error_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. Run fresh migration:
   ```bash
   php artisan migrate:fresh
   ```

### Character Set Issues

**Problem**: Emoji or special characters not saving

**Solution**: Ensure database uses `utf8mb4`:
```sql
ALTER DATABASE error_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## Switching Back to SQLite (if needed)

If you want to switch back to SQLite:

1. Update `.env`:
   ```env
   DB_CONNECTION=sqlite
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=error_dashboard
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

2. Create SQLite file:
   ```bash
   touch database/database.sqlite
   ```

3. Clear config and migrate:
   ```bash
   php artisan config:clear
   php artisan migrate:fresh
   ```

---

## Production Recommendations

### 1. Use Strong Password

Update `.env` in production:
```env
DB_USERNAME=your_secure_user
DB_PASSWORD=your_strong_password
```

### 2. Create Dedicated User

```sql
CREATE USER 'error_dashboard_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON error_dashboard.* TO 'error_dashboard_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Enable Query Logging (Development Only)

In `config/database.php`:
```php
'mysql' => [
    // ...
    'options' => [
        PDO::ATTR_EMULATE_PREPARES => true,
    ],
],
```

### 4. Connection Pooling

For high traffic, consider using connection pooling:
```env
DB_POOL_MIN=2
DB_POOL_MAX=10
```

### 5. Regular Backups

Set up automated backups:
```bash
# Add to crontab
0 2 * * * mysqldump -u root error_dashboard > /backups/error_dashboard_$(date +\%Y\%m\%d).sql
```

---

## MySQL Configuration for Laravel

### Recommended my.cnf Settings

```ini
[mysqld]
# Character Set
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci

# Performance
max_connections=200
innodb_buffer_pool_size=256M
innodb_log_file_size=64M

# Query Cache (MySQL 5.7 and below)
query_cache_type=1
query_cache_size=32M
```

---

## Monitoring

### Check Active Connections

```sql
SHOW PROCESSLIST;
```

### Check Table Sizes

```sql
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'error_dashboard'
ORDER BY (data_length + index_length) DESC;
```

### Check Slow Queries

Enable slow query log in `my.cnf`:
```ini
slow_query_log=1
slow_query_log_file=/var/log/mysql/slow-query.log
long_query_time=2
```

---

## Summary

✅ **MySQL Database**: `error_dashboard` created
✅ **Connection**: Configured in `.env`
✅ **Migrations**: All tables created successfully
✅ **Server**: Running on http://127.0.0.1:8000
✅ **Cache**: Using database driver
✅ **Sessions**: Using database driver
✅ **Queue**: Using database driver

Your Laravel Error Dashboard is now fully configured with MySQL and ready to use!

---

## Next Steps

1. ✅ MySQL configured
2. ✅ Database created
3. ✅ Migrations run
4. ✅ Server restarted
5. 🎯 Configure your Error API in `.env`
6. 🎯 Test the dashboard at http://127.0.0.1:8000

---

**Need Help?**

- Check Laravel logs: `storage/logs/laravel.log`
- Check MySQL logs: `/var/log/mysql/error.log`
- Run diagnostics: `php artisan about`

Happy monitoring! 🚀
