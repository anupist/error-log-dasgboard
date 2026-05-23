# 🚀 Quick Reference Guide

## Common Commands

### Server Management
```bash
# Start the development server
cd error-dashboard
php artisan serve

# Start on a different port
php artisan serve --port=8080

# Stop the server
# Press Ctrl+C in the terminal
```

### Testing
```bash
# Test API connection
php artisan test:api

# Run all tests
php artisan test
```

### Cache Management
```bash
# Clear all cache
php artisan cache:clear

# Clear specific cache
php artisan cache:forget error_logs_2026-05-20

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear
```

### Asset Management
```bash
# Build for production
npm run build

# Development mode with hot reload
npm run dev

# Watch for changes
npm run watch
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh migration (drop all tables)
php artisan migrate:fresh

# Rollback last migration
php artisan migrate:rollback
```

---

## Configuration Quick Edit

### Change API Endpoint
Edit `.env` file:
```env
ERROR_API_BASE_URL=http://your-api-domain.com
```

### Change Cache Duration
Edit `.env` file:
```env
ERROR_API_CACHE_SECONDS=120  # 2 minutes
```

### Change Auto-Refresh Interval
Edit `.env` file:
```env
ERROR_AUTO_REFRESH=60  # 60 seconds
```

### Change Pagination
Edit `app/Livewire/Errors/RecentErrorsTable.php`:
```php
public $perPage = 50;  // Show 50 items per page
```

---

## Troubleshooting Quick Fixes

### Dashboard Shows Blank Page
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild assets
npm run build

# Restart server
# Ctrl+C then php artisan serve
```

### API Not Connecting
```bash
# Test the connection
php artisan test:api

# Check logs
cat storage/logs/laravel.log

# Verify .env configuration
cat .env | grep ERROR_API
```

### Charts Not Loading
```bash
# Rebuild assets
npm run build

# Check if node_modules exists
ls node_modules

# Reinstall if needed
npm install
```

### Pagination Not Working
The custom pagination is already implemented. If issues persist:
1. Clear browser cache
2. Hard refresh (Ctrl+F5)
3. Check browser console for errors

---

## File Locations

### Configuration
- **Environment:** `.env`
- **API Config:** `config/error-api.php`
- **Database Config:** `config/database.php`

### Services
- **API Service:** `app/Services/Api/ErrorApiService.php`
- **Categorizer:** `app/Services/ErrorAnalyzer/ErrorCategorizer.php`

### Livewire Components
- **Dashboard:** `app/Livewire/Dashboard/`
- **Errors:** `app/Livewire/Errors/`

### Views
- **Layout:** `resources/views/layouts/app.blade.php`
- **Livewire Views:** `resources/views/livewire/`

### Vue Components
- **Charts:** `resources/js/components/charts/`

### Logs
- **Laravel Logs:** `storage/logs/laravel.log`

---

## API Response Format

Your API should return data in this format:

```json
{
  "data": "[{\"id\":\"1\",\"exception\":\"ErrorException\",\"message\":\"Error message\",\"file\":\"/path/to/file.php\",\"line\":123,\"trace\":\"Stack trace...\",\"occurred_at\":\"2026-05-20 20:21:58\"}]"
}
```

Or as an array:
```json
{
  "data": [
    {
      "id": "1",
      "exception": "ErrorException",
      "message": "Error message",
      "file": "/path/to/file.php",
      "line": 123,
      "trace": "Stack trace...",
      "occurred_at": "2026-05-20 20:21:58"
    }
  ]
}
```

Both formats are supported!

---

## Browser Access

**Development Server:** http://127.0.0.1:8000

**Alternative URLs:**
- http://localhost:8000
- http://127.0.0.1:8000

---

## Performance Tips

### Optimize Cache
```env
# Increase cache duration for production
ERROR_API_CACHE_SECONDS=300  # 5 minutes
```

### Reduce Auto-Refresh
```env
# Less frequent updates
ERROR_AUTO_REFRESH=60  # 1 minute
```

### Pagination
```php
// Show fewer items per page for faster loading
public $perPage = 10;
```

---

## Security Checklist

- [ ] Change `APP_KEY` in production
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use HTTPS for API endpoints
- [ ] Implement authentication (if needed)
- [ ] Restrict database access
- [ ] Use environment-specific `.env` files
- [ ] Enable CSRF protection
- [ ] Sanitize user inputs

---

## Backup Important Files

Before making changes, backup:
1. `.env` - Environment configuration
2. `config/error-api.php` - API settings
3. `database/` - Database files
4. Custom modifications in `app/` and `resources/`

---

## Getting Help

### Check Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Web server logs (if using Apache/Nginx)
# Check your web server's error log location
```

### Debug Mode
Enable in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Test Individual Components
```bash
# Test API service
php artisan tinker
>>> $service = app(\App\Services\Api\ErrorApiService::class);
>>> $errors = $service->getTodayErrors();
>>> $errors->count();
```

---

## Quick Customization

### Change Theme Colors
Edit `tailwind.config.js`:
```js
colors: {
  primary: {
    // Your custom colors
  }
}
```

Then rebuild:
```bash
npm run build
```

### Add New Error Category
Edit `app/Services/ErrorAnalyzer/ErrorCategorizer.php`:
```php
'your-pattern' => [
    'category' => 'your-category',
    'severity' => 'error'
],
```

### Modify Table Columns
Edit `resources/views/livewire/errors/recent-errors-table.blade.php`

---

## Production Deployment

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false

# 2. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Build assets
npm run build

# 4. Set permissions
chmod -R 755 storage bootstrap/cache
```

---

**Last Updated:** May 20, 2026
