import '../css/app.css';
import './bootstrap';
import { createApp } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Import Vue components
import ErrorTrendChart from './components/charts/ErrorTrendChart.vue';
import ErrorCategoryChart from './components/charts/ErrorCategoryChart.vue';

// Create Vue app
const app = createApp({});

// Register ApexCharts
app.use(VueApexCharts);

// Register components globally
app.component('error-trend-chart', ErrorTrendChart);
app.component('error-category-chart', ErrorCategoryChart);

// Mount Vue app
app.mount('#app');
