<template>
  <div class="error-trend-chart">
    <apexchart
      type="area"
      height="350"
      :options="chartOptions"
      :series="series"
    ></apexchart>
  </div>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
  name: 'ErrorTrendChart',
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
    const series = computed(() => [{
      name: 'Errors',
      data: props.data
    }]);

    const chartOptions = computed(() => ({
      chart: {
        type: 'area',
        height: 350,
        toolbar: {
          show: true,
          tools: {
            download: true,
            selection: false,
            zoom: false,
            zoomin: false,
            zoomout: false,
            pan: false,
            reset: false
          }
        },
        background: 'transparent',
        foreColor: props.darkMode ? '#e5e7eb' : '#374151'
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 2
      },
      fill: {
        type: 'gradient',
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.7,
          opacityTo: 0.3,
          stops: [0, 90, 100]
        }
      },
      xaxis: {
        categories: props.labels,
        labels: {
          style: {
            colors: props.darkMode ? '#9ca3af' : '#6b7280'
          }
        }
      },
      yaxis: {
        labels: {
          style: {
            colors: props.darkMode ? '#9ca3af' : '#6b7280'
          }
        }
      },
      colors: ['#3b82f6'],
      grid: {
        borderColor: props.darkMode ? '#374151' : '#e5e7eb',
        strokeDashArray: 4
      },
      tooltip: {
        theme: props.darkMode ? 'dark' : 'light',
        x: {
          format: 'HH:mm'
        }
      }
    }));

    return {
      series,
      chartOptions
    };
  }
};
</script>

<style scoped>
.error-trend-chart {
  width: 100%;
}
</style>
