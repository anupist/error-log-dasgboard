<div>
    <div data-vue-chart
         x-data="{
             darkMode: localStorage.getItem('darkMode') === 'true',
             chartLabels: {{ json_encode($chartData['labels'] ?? []) }},
             chartValues: {{ json_encode($chartData['data'] ?? []) }}
         }">
        <error-trend-chart
            :labels="chartLabels"
            :data="chartValues"
            :dark-mode="darkMode"
        ></error-trend-chart>
    </div>

    <div class="mt-6" data-vue-chart
         x-data="{
             darkMode: localStorage.getItem('darkMode') === 'true',
             catLabels: {{ json_encode($categoryData['labels'] ?? []) }},
             catValues: {{ json_encode($categoryData['data'] ?? []) }}
         }">
        <error-category-chart
            :labels="catLabels"
            :data="catValues"
            :dark-mode="darkMode"
        ></error-category-chart>
    </div>
</div>
