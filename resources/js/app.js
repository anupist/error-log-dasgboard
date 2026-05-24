import '../css/app.css';
import './bootstrap';
import { createApp } from 'vue';
import VueApexCharts from 'vue3-apexcharts';

// Import Vue chart components
import ErrorTrendChart from './components/charts/ErrorTrendChart.vue';
import ErrorCategoryChart from './components/charts/ErrorCategoryChart.vue';

// Mount Vue only on chart containers, NOT on #app (which is owned by Livewire/Alpine)
// We use a dedicated mount point so Vue and Alpine don't conflict.
document.addEventListener('DOMContentLoaded', () => {
    mountCharts();
});

// Re-mount after Livewire navigates (wire:navigate SPA mode)
document.addEventListener('livewire:navigated', () => {
    mountCharts();
});

function mountCharts() {
    document.querySelectorAll('[data-vue-chart]').forEach(el => {
        // Avoid double-mounting
        if (el.__vue_app__) return;

        const app = createApp({});
        app.use(VueApexCharts);
        app.component('error-trend-chart', ErrorTrendChart);
        app.component('error-category-chart', ErrorCategoryChart);
        app.mount(el);
    });
}
