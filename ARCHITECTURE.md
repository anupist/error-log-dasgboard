# Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Browser (Client)                          │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Vue 3 Components                       │  │
│  │  ┌─────────────────┐      ┌──────────────────────┐      │  │
│  │  │ ErrorTrendChart │      │ ErrorCategoryChart   │      │  │
│  │  │   (ApexCharts)  │      │    (ApexCharts)      │      │  │
│  │  └─────────────────┘      └──────────────────────┘      │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              Livewire 4 Components                        │  │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │  │
│  │  │ Stats Cards  │  │ Error Table  │  │   Filters    │  │  │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                  Tailwind CSS + Alpine.js                 │  │
│  │              (Styling + Dark Mode Logic)                  │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              ↕ HTTP/WebSocket
┌─────────────────────────────────────────────────────────────────┐
│                    Laravel 12 Application                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                      Routes Layer                         │  │
│  │              (web.php - Dashboard Route)                  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              ↓                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                  Livewire Controllers                     │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  DashboardOverview (Main Container)              │   │  │
│  │  │  - Manages refresh interval                      │   │  │
│  │  │  - Dispatches events                             │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorStatsCards (Statistics)                    │   │  │
│  │  │  - Calculates metrics                            │   │  │
│  │  │  - Listens to refresh events                     │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorTrendChart (Chart Data)                    │   │  │
│  │  │  - Prepares hourly data                          │   │  │
│  │  │  - Formats for ApexCharts                        │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  RecentErrorsTable (Table + Pagination)          │   │  │
│  │  │  - Handles filtering                             │   │  │
│  │  │  - Manages pagination                            │   │  │
│  │  │  - Opens modal                                   │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorFilters (Category Filters)                 │   │  │
│  │  │  - Dispatches filter events                      │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorSearch (Search Input)                      │   │  │
│  │  │  - Debounced search                              │   │  │
│  │  │  - Dispatches search events                      │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              ↓                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Service Layer                          │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorApiService                                 │   │  │
│  │  │  - HTTP client wrapper                           │   │  │
│  │  │  - Retry logic                                   │   │  │
│  │  │  - Timeout handling                              │   │  │
│  │  │  - Response parsing                              │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  │                       ↓                                   │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorCategorizer                                │   │  │
│  │  │  - Pattern matching                              │   │  │
│  │  │  - Category assignment                           │   │  │
│  │  │  - Severity detection                            │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              ↓                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                      DTO Layer                            │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │  ErrorLogDTO                                     │   │  │
│  │  │  - Typed properties                              │   │  │
│  │  │  - Helper methods                                │   │  │
│  │  │  - Immutable data                                │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                              ↓                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Cache Layer                            │  │
│  │              (60 second default TTL)                      │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              ↕ HTTPS
┌─────────────────────────────────────────────────────────────────┐
│                    External Error API                            │
│              (Your Laravel Error Logging Service)                │
│         GET /public-api/log-errors/laravel                       │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow

### 1. Initial Page Load

```
User → Browser
  ↓
Browser → Laravel Route (/)
  ↓
Route → DashboardOverview Component
  ↓
DashboardOverview → Renders Child Components
  ↓
Child Components → Call Services
  ↓
ErrorApiService → Check Cache
  ↓
Cache Miss → HTTP Request to External API
  ↓
External API → Returns JSON
  ↓
ErrorApiService → Parse & Cache Response
  ↓
ErrorCategorizer → Categorize Errors
  ↓
ErrorLogDTO → Transform to DTOs
  ↓
Components → Render with Data
  ↓
Browser → Display Dashboard
```

### 2. Auto Refresh (Every 30s)

```
Livewire Poll Timer → Triggers
  ↓
DashboardOverview → Dispatches 'refresh-dashboard' Event
  ↓
All Components → Listen to Event
  ↓
Components → Call Services Again
  ↓
ErrorApiService → Check Cache
  ↓
Cache Hit → Return Cached Data (if < 60s)
Cache Miss → Fetch Fresh Data
  ↓
Components → Update UI
  ↓
Browser → Smooth Update (No Page Reload)
```

### 3. User Interaction (Filter/Search)

```
User → Clicks Filter or Types Search
  ↓
ErrorFilters/ErrorSearch → Dispatches Event
  ↓
RecentErrorsTable → Listens to Event
  ↓
RecentErrorsTable → Filters Data Client-Side
  ↓
RecentErrorsTable → Re-renders Table
  ↓
Browser → Shows Filtered Results (Instant)
```

### 4. View Error Details

```
User → Clicks "View Details"
  ↓
RecentErrorsTable → Sets $selectedError
  ↓
Modal → Opens with Error Data
  ↓
Browser → Displays Modal Overlay
```

## Component Communication

### Livewire Events

```
DashboardOverview
  ↓ dispatch('refresh-dashboard')
  ├→ ErrorStatsCards (listens)
  ├→ ErrorTrendChart (listens)
  └→ RecentErrorsTable (listens)

ErrorFilters
  ↓ dispatch('filter-changed', category)
  └→ RecentErrorsTable (listens)

ErrorSearch
  ↓ dispatch('search-updated', search)
  └→ RecentErrorsTable (listens)
```

### Vue Props

```
ErrorTrendChart.blade.php
  ↓ passes data as props
  └→ ErrorTrendChart.vue (receives)
      - labels: Array
      - data: Array
      - darkMode: Boolean

ErrorTrendChart.blade.php
  ↓ passes data as props
  └→ ErrorCategoryChart.vue (receives)
      - labels: Array
      - data: Array
      - darkMode: Boolean
```

## Caching Strategy

```
┌─────────────────────────────────────────┐
│         Cache Key Structure              │
├─────────────────────────────────────────┤
│  error_logs_{date}                      │
│  error_summary_{date}-{hour}            │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│         Cache Storage                    │
│  (File/Redis/Memcached)                 │
│  TTL: 60 seconds                        │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│      Cache Invalidation                  │
│  - Automatic after TTL                  │
│  - Manual: cache:clear                  │
│  - Per-date granularity                 │
└─────────────────────────────────────────┘
```

## Security Layers

```
┌─────────────────────────────────────────┐
│         Input Layer                      │
│  - Search input sanitization            │
│  - Filter validation                    │
│  - Type checking                        │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│       Processing Layer                   │
│  - DTO type safety                      │
│  - Service validation                   │
│  - Error handling                       │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│        Output Layer                      │
│  - Blade escaping                       │
│  - XSS protection                       │
│  - Safe HTML rendering                  │
└─────────────────────────────────────────┘
```

## Performance Optimizations

### 1. Caching
- API responses cached for 60 seconds
- Reduces external API calls
- Improves response time

### 2. Debouncing
- Search input debounced 500ms
- Reduces unnecessary filtering
- Improves UX

### 3. Client-Side Filtering
- Filter/search happens in browser
- No server round-trip
- Instant results

### 4. Lazy Loading
- Charts load on demand
- Modal content loaded when opened
- Reduces initial payload

### 5. Pagination
- Only 20 items rendered at once
- Reduces DOM size
- Faster rendering

## Technology Stack Layers

```
┌─────────────────────────────────────────┐
│         Presentation Layer               │
│  - Blade Templates                      │
│  - Tailwind CSS                         │
│  - Alpine.js (Dark Mode)                │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│       Component Layer                    │
│  - Livewire 4 (Server-side)             │
│  - Vue 3 (Client-side)                  │
│  - ApexCharts (Visualization)           │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│        Business Logic Layer              │
│  - Service Classes                      │
│  - DTOs                                 │
│  - Categorization Logic                 │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│         Data Layer                       │
│  - HTTP Client                          │
│  - Cache                                │
│  - External API                         │
└─────────────────────────────────────────┘
```

## Deployment Architecture

```
┌─────────────────────────────────────────┐
│            Web Server                    │
│         (Nginx/Apache)                  │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│         PHP-FPM / PHP                    │
│       (Laravel Application)              │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│          Cache Server                    │
│      (Redis/Memcached)                  │
└─────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────┐
│         External API                     │
│    (Error Logging Service)              │
└─────────────────────────────────────────┘
```

## File Organization

```
app/
├── DTOs/                    # Data Transfer Objects
├── Livewire/               # Livewire Components
│   ├── Dashboard/          # Dashboard-specific
│   └── Errors/             # Error-specific
├── Services/               # Business Logic
│   ├── Api/                # API Integration
│   └── ErrorAnalyzer/      # Error Processing
└── Providers/              # Service Registration

resources/
├── css/                    # Styles
├── js/                     # JavaScript
│   └── components/         # Vue Components
│       └── charts/         # Chart Components
└── views/                  # Blade Templates
    ├── layouts/            # Layout Templates
    └── livewire/           # Livewire Views

config/
└── error-api.php           # API Configuration

routes/
└── web.php                 # Web Routes
```

## Key Design Patterns

1. **Service Layer Pattern**: Business logic separated from controllers
2. **DTO Pattern**: Type-safe data transfer
3. **Repository Pattern**: Data access abstraction (via services)
4. **Observer Pattern**: Livewire events for component communication
5. **Strategy Pattern**: Error categorization logic
6. **Singleton Pattern**: Service registration in container
7. **Factory Pattern**: DTO creation from arrays

## Scalability Considerations

- **Horizontal Scaling**: Stateless design allows multiple instances
- **Caching**: Reduces load on external API
- **Async Processing**: Can add queue for heavy operations
- **CDN**: Static assets can be served from CDN
- **Database**: Can add database for historical data
- **Load Balancing**: Multiple app servers supported

---

This architecture provides a solid foundation for a production-ready error monitoring dashboard with room for future enhancements.
