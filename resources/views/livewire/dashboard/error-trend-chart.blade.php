<div x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true',
    chartData: @js($chartData ?? ['labels' => [], 'data' => []]),
    categoryData: @js($categoryData ?? ['labels' => [], 'data' => []])
}" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
    <!-- Error Trend Chart -->
    <div class="mb-8">
        <error-trend-chart 
            :labels="chartData?.labels || []" 
            :data="chartData?.data || []"
            :dark-mode="darkMode"
        ></error-trend-chart>
    </div>

    <!-- Category Chart -->
    <div>
        <error-category-chart 
            :labels="categoryData?.labels || []" 
            :data="categoryData?.data || []"
            :dark-mode="darkMode"
        ></error-category-chart>
    </div>
</div>
