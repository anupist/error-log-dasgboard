# Testing Guide

## Quick Start

The application is now running at: **http://127.0.0.1:8000**

## Testing with Mock Data

Since the dashboard consumes an external API, you have two options for testing:

### Option 1: Configure Your Real API

Update `.env` with your actual error API endpoint:

```env
ERROR_API_BASE_URL=https://your-actual-api.com
ERROR_API_TIMEOUT=15
ERROR_API_CACHE_SECONDS=60
ERROR_API_RETRY=3
ERROR_AUTO_REFRESH=30
```

### Option 2: Create a Mock API Endpoint

For testing purposes, you can create a mock endpoint in your routes:

**File: `routes/web.php`**

Add this route for testing:

```php
Route::get('/mock-api/log-errors/laravel', function () {
    return response()->json([
        'data' => [
            [
                'id' => uniqid(),
                'message' => 'SQLSTATE[42S02]: Base table or view not found',
                'exception' => 'Illuminate\Database\QueryException',
                'file' => '/var/www/app/Models/User.php',
                'line' => 45,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(5)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'Undefined variable $user',
                'exception' => 'ErrorException',
                'file' => '/var/www/app/Http/Controllers/UserController.php',
                'line' => 78,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(10)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'The given data was invalid',
                'exception' => 'Illuminate\Validation\ValidationException',
                'file' => '/var/www/app/Http/Requests/StoreUserRequest.php',
                'line' => 32,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(15)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'Unauthenticated',
                'exception' => 'Illuminate\Auth\AuthenticationException',
                'file' => '/var/www/app/Http/Middleware/Authenticate.php',
                'line' => 67,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(20)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'cURL error 28: Connection timeout',
                'exception' => 'GuzzleHttp\Exception\ConnectException',
                'file' => '/var/www/app/Services/ExternalApiService.php',
                'line' => 123,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(25)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'Queue job failed after 3 attempts',
                'exception' => 'Illuminate\Queue\MaxAttemptsExceededException',
                'file' => '/var/www/app/Jobs/ProcessOrder.php',
                'line' => 89,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subMinutes(30)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'RedisException: Connection refused',
                'exception' => 'RedisException',
                'file' => '/var/www/app/Services/CacheService.php',
                'line' => 45,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subHours(1)->toIso8601String(),
            ],
            [
                'id' => uniqid(),
                'message' => 'Call to undefined method',
                'exception' => 'BadMethodCallException',
                'file' => '/var/www/app/Helpers/StringHelper.php',
                'line' => 156,
                'trace' => 'Stack trace here...',
                'occurred_at' => now()->subHours(2)->toIso8601String(),
            ],
        ]
    ]);
});
```

Then update `.env`:

```env
ERROR_API_BASE_URL=http://127.0.0.1:8000
```

And update `config/error-api.php`:

```php
'endpoints' => [
    'errors' => '/mock-api/log-errors/laravel',
],
```

## Testing Features

### 1. Dashboard Overview
- Visit: http://127.0.0.1:8000
- Check stats cards showing total errors, critical errors, error rate, and categories
- Verify auto-refresh works (every 30 seconds by default)

### 2. Error Trend Chart
- Should display a 24-hour area chart
- Hover over data points to see error counts
- Check dark mode toggle affects chart colors

### 3. Error Category Chart
- Should display a donut chart with error distribution
- Click legend items to toggle categories
- Verify total count in center

### 4. Filters
- Click category buttons to filter errors
- Verify "Clear Filter" button appears when filter is active
- Check filtered results update immediately

### 5. Search
- Type in search box (debounced 500ms)
- Search works on error message and exception name
- Clear button (X) appears when search has text

### 6. Error Table
- Displays recent errors with pagination
- Click "View Details" to open modal
- Modal shows full error information
- Copy button for stack trace

### 7. Dark Mode
- Toggle dark/light mode using button in sidebar
- Preference persists in localStorage
- All components adapt to theme

### 8. Manual Refresh
- Click "Refresh" button in top right
- All components reload data
- Loading states should be visible

### 9. Responsive Design
- Test on mobile viewport (< 768px)
- Sidebar should adapt
- Tables should scroll horizontally
- Charts should resize

## Performance Testing

### Cache Testing
1. First load: API call made
2. Refresh within cache period: Data from cache
3. Wait for cache expiry: New API call

Check cache in:
```bash
php artisan cache:clear  # Clear all cache
```

### Polling Testing
1. Leave dashboard open
2. Watch network tab
3. Verify requests every 30 seconds (or your configured interval)

## Debugging

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Clear Everything
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Rebuild Assets
```bash
npm run build
```

## Expected Behavior

✅ **Working Correctly:**
- Stats cards show numbers
- Charts render with data
- Filters work instantly
- Search is debounced
- Modal opens/closes smoothly
- Dark mode toggles
- Auto-refresh updates data
- Pagination works

❌ **Common Issues:**
- Empty dashboard: Check API URL and response format
- Charts not showing: Verify Vue components loaded
- Filters not working: Check Livewire events
- Dark mode broken: Clear localStorage

## Browser Console

Open browser DevTools (F12) and check:
- No JavaScript errors
- Livewire is loaded
- Vue components mounted
- API requests successful

## Production Checklist

Before deploying:
- [ ] Set correct `ERROR_API_BASE_URL`
- [ ] Configure cache driver (Redis recommended)
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up proper logging
- [ ] Configure queue workers if needed
- [ ] Set up SSL certificate
- [ ] Configure CORS if API is on different domain

## Next Steps

1. **Customize**: Adjust colors, add your logo
2. **Extend**: Add more error categories
3. **Integrate**: Connect to your real error API
4. **Deploy**: Set up on production server
5. **Monitor**: Watch for performance issues
6. **Optimize**: Tune cache and polling settings

Enjoy your error monitoring dashboard! 🚀
