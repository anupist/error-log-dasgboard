<template>
  <div class="error-category-chart">
    <apexchart
      type="donut"
      height="350"
      :options="chartOptions"
      :series="series"
    ></apexchart>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  name: 'ErrorCategoryChart',
  props: {
    labels: {
      type: Array,
      default: () => []
    },
    data: {
      type: Array,
      default: () => []
    },
    darkMode: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const series = computed(() => props.data);

    const chartOptions = computed(() => ({
      chart: {
        type: 'donut',
        height: 350,
        background: 'transparent',
        foreColor: props.darkMode ? '#e5e7eb' : '#374151'
      },
      labels: props.labels,
      colors: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#14b8a6'],
      legend: {
        position: 'bottom',
        labels: {
          colors: props.darkMode ? '#e5e7eb' : '#374151'
        }
      },
      plotOptions: {
        pie: {
          donut: {
            size: '70%',
            labels: {
              show: true,
              name: {
                show: true,
                fontSize: '16px',
                fontWeight: 600,
                color: props.darkMode ? '#e5e7eb' : '#374151'
              },
              value: {
                show: true,
                fontSize: '24px',
                fontWeight: 700,
                color: props.darkMode ? '#e5e7eb' : '#374151',
                formatter: function (val) {
                  return val;
                }
              },
              total: {
                show: true,
                label: 'Total Errors',
                fontSize: '14px',
                fontWeight: 600,
                color: props.darkMode ? '#9ca3af' : '#6b7280',
                formatter: function (w) {
                  return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                }
              }
            }
          }
        }
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '12px',
          fontWeight: 600
        },
        dropShadow: {
          enabled: false
        }
      },
      tooltip: {
        theme: props.darkMode ? 'dark' : 'light',
        y: {
          formatter: function (val) {
            return val + ' errors';
          }
        }
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            height: 300
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    }));

    return {
      series,
      chartOptions
    };
  }
};
</script>

<style scoped>
.error-category-chart {
  width: 100%;
}
</style>
