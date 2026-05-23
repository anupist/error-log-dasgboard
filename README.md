# Error Dashboard - Laravel Error Monitoring

A modern, real-time error monitoring dashboard built with Laravel 12, Livewire 4, Vue 3, Tailwind CSS, and ApexCharts.

## Features

✨ **Real-time Error Monitoring**
- Auto-refresh every 30 seconds
- Live error tracking from external API
- Manual refresh capability

📊 **Rich Visualizations**
- Error trend charts (24-hour view)
- Category distribution (donut chart)
- Statistics cards with key metrics
- Responsive ApexCharts integration

🔍 **Advanced Filtering**
- Search by error message or exception
- Filter by error category
- Pagination support
- Debounced search for performance

🎨 **Modern UI/UX**
- Clean, SaaS-style interface
- Dark/Light mode support
- Mobile responsive design
- Smooth transitions and animations

🔐 **Security Features**
- XSS protection
- Content sanitization
- API response validation
- Secure error display

⚡ **Performance Optimized**
- API response caching
- Lazy loading
- Efficient polling
- Smart refresh optimization

## Tech Stack

- **Backend**: Laravel 12
- **Frontend Framework**: Livewire 4
- **JavaScript Framework**: Vue 3
- **Styling**: Tailwind CSS v4
- **Charts**: ApexCharts + vue3-apexcharts
- **Build Tool**: Vite
- **Database**: SQLite (default)

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (or your preferred database)

### Setup Steps

1. **Install Dependencies**
```bash
composer install
npm install
```

2. **Environment Configuration**
Copy `.env.example` to `.env` and configure:
```env
ERROR_API_BASE_URL=https://example.com
ERROR_API_TIMEOUT=15
ERROR_API_CACHE_SECONDS=60
ERROR_API_RETRY=3
ERROR_AUTO_REFRESH=30
```

3. **Generate Application Key**
```bash
php artisan key:generate
```

4. **Run Migrations**
```bash
php artisan migrate
```

5. **Build Assets**
```bash
npm run build
# or for development
npm run dev
```

6. **Start Development Server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Configuration

### Error API Configuration

Edit `config/error-api.php`:

```php
return [
    'base_url' => env('ERROR_API_BASE_URL', 'https://example.com'),
    'timeout' => env('ERROR_API_TIMEOUT', 15),
    'cache_seconds' => env('ERROR_API_CACHE_SECONDS', 60),
    'retry' => env('ERROR_API_RETRY', 3),
    'auto_refresh' => env('ERROR_AUTO_REFRESH', 30),
    'endpoints' => [
        'errors' => '/public-api/log-errors/laravel',
    ],
];
```

### Auto-Refresh Interval

Change the polling interval in `.env`:
```env
ERROR_AUTO_REFRESH=30  # seconds
```

## Project Structure

```
error-dashboard/
├── app/
│   ├── DTOs/
│   │   └── ErrorLogDTO.php
│   ├── Services/
│   │   ├── Api/
│   │   │   └── ErrorApiService.php
│   │   └── ErrorAnalyzer/
│   │       └── ErrorCategorizer.php
│   └── Livewire/
│       ├── Dashboard/
│       │   ├── DashboardOverview.php
│       │   ├── ErrorStatsCards.php
│       │   └── ErrorTrendChart.php
│       └── Errors/
│           ├── RecentErrorsTable.php
│           ├── ErrorFilters.php
│           └── ErrorSearch.php
├── resources/
│   ├── js/
│   │   ├── components/
│   │   │   └── charts/
│   │   │       ├── ErrorTrendChart.vue
│   │   │       └── ErrorCategoryChart.vue
│   │   └── app.js
│   ├── css/
│   │   └── app.css
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       └── livewire/
│           ├── dashboard/
│           └── errors/
└── config/
    └── error-api.php
```

## Error Categories

The system automatically categorizes errors:

- **Database**: SQL errors, QueryException, PDOException
- **PHP**: Undefined variables, TypeError, ParseError
- **Validation**: ValidationException
- **Authentication**: AuthenticationException, Unauthorized
- **API**: Guzzle errors, cURL errors
- **Queue**: Job failures, timeouts
- **Cache**: Redis, Memcached errors
- **General**: Uncategorized errors

## Error Severity Levels

- **Critical**: Database errors, fatal errors, parse errors
- **Error**: General exceptions and errors
- **Warning**: Warnings and deprecations
- **Info**: Informational messages

## API Response Format

Expected API response structure:

```json
{
  "data": [
    {
      "id": "unique-id",
      "message": "Error message",
      "exception": "ExceptionClass",
      "file": "/path/to/file.php",
      "line": 123,
      "trace": "Stack trace...",
      "occurred_at": "2026-05-20T10:30:00Z",
      "context": {}
    }
  ]
}
```

Alternative formats supported:
- `{ "errors": [...] }`
- Direct array: `[...]`

## Development

### Run Development Server
```bash
npm run dev
php artisan serve
```

### Clear Cache
```bash
php artisan cache:clear
```

### Run Tests
```bash
php artisan test
```

## Customization

### Change Theme Colors

Edit `tailwind.config.js` to customize colors:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        // Your custom colors
      },
    },
  },
}
```

### Modify Auto-Refresh

Edit the Livewire component:
```php
// app/Livewire/Dashboard/DashboardOverview.php
public function mount()
{
    $this->refreshInterval = 60; // 60 seconds
}
```

### Add Custom Error Categories

Edit `app/Services/ErrorAnalyzer/ErrorCategorizer.php`:

```php
private const PATTERNS = [
    'your-category' => [
        'pattern1',
        'pattern2',
    ],
];
```

## Performance Tips

1. **Increase Cache Duration**: Set `ERROR_API_CACHE_SECONDS` higher
2. **Reduce Polling Frequency**: Increase `ERROR_AUTO_REFRESH`
3. **Enable Redis**: Use Redis for better cache performance
4. **Optimize API**: Ensure your error API responds quickly

## Troubleshooting

### Errors Not Showing
- Check API URL in `.env`
- Verify API is accessible
- Check Laravel logs: `storage/logs/laravel.log`
- Clear cache: `php artisan cache:clear`

### Charts Not Rendering
- Ensure Vue is properly loaded
- Check browser console for errors
- Rebuild assets: `npm run build`

### Dark Mode Not Working
- Clear browser localStorage
- Check Alpine.js is loaded
- Verify Tailwind dark mode classes

## License

This project is open-sourced software.

## Support

For issues and questions, please check the documentation or create an issue.
