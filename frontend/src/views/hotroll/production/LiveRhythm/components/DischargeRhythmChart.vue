<template>
  <div :class="className" :style="{height:height,width:width}" />
</template>

<script>
import echarts from 'echarts'
require('echarts/theme/macarons') // echarts theme
import resize from './mixins/resize'

export default {
  mixins: [resize],
  props: {
    className: {
      type: String,
      default: 'chart'
    },
    width: {
      type: String,
      default: '100%'
    },
    height: {
      type: String,
      default: '350px'
    },
    autoResize: {
      type: Boolean,
      default: true
    },
    chartData: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      chart: null
    }
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.setOptions(val)
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.initChart()
    })
  },
  beforeDestroy() {
    if (!this.chart) {
      return
    }
    this.chart.dispose()
    this.chart = null
  },
  methods: {
    initChart() {
      this.chart = echarts.init(this.$el, 'macarons')
      this.setOptions(this.chartData)
    },
    setOptions({ xAxisData, expectedData, actualData } = {}) {
      console.log('------------ in lineChart ----------------')
      console.log(xAxisData)
      console.log(expectedData)
      console.log(actualData)
      this.chart.setOption({
        title: {
          text: '热轧出炉节奏',
          subtext: '每块板坯出炉间隔秒数'
        },
        xAxis: {
          type: 'category',
          data: xAxisData,
          boundaryGap: false,
          axisTick: {
            show: false
          }
        },
        grid: {
          left: 10,
          right: 10,
          bottom: 20,
          top: 30,
          containLabel: true
        },
        tooltip: {
          trigger: 'axis',
          axisPointer: {
            type: 'cross'
          },
          padding: [5, 10]
        },
        yAxis: {
          max: 250,
          min: 'dataMin',
          axisTick: {
            show: false
          }
        },
        legend: {
          data: ['目标节奏', '实际节奏']
        },
        series: [{
          name: '目标节奏', itemStyle: {
            normal: {
              color: '#FF005A',
              lineStyle: {
                color: '#FF005A',
                width: 2
              }
            }
          },
          // smooth: true,
          step: 'middle',
          type: 'line',
          data: expectedData
          // animationDuration: 2800,
          // animationEasing: 'cubicInOut'
        },
        {
          name: '实际节奏',
          // smooth: true,
          step: 'middle',
          type: 'line',
          itemStyle: {
            normal: {
              color: '#3888fa',
              lineStyle: {
                color: '#3888fa',
                width: 2
              },
              areaStyle: {
                color: '#f3f8ff'
              }
            }
          },
          data: actualData
          // animationDuration: 1800,
          // animationEasing: 'quadraticOut'
        }]
      })
    }
  }
}
</script>
