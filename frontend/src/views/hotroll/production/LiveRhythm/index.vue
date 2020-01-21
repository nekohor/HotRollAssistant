<template>
  <div class="dashboard-editor-container">

    <panel-group @handleSetChartData="handleSetChartData" />

    <el-row style="background:#fff;padding:16px 16px 0;margin-bottom:32px;">
      <discharge-rhythm-chart :chart-data="dischargeRhythmChartData" />
    </el-row>

    <!-- <el-row :gutter="32">
      <el-col :xs="24" :sm="24" :lg="8">
        <div class="chart-wrapper">
          <bar-chart />
        </div>
      </el-col>
    </el-row> -->
  </div>
</template>

<script>

import dayjs from 'dayjs'
import PanelGroup from './components/PanelGroup'
import DischargeRhythmChart from './components/DischargeRhythmChart'
import { getDischargeRhythm } from '@/api/production'

export default {
  name: 'LiveRhythm',
  components: {
    PanelGroup,
    DischargeRhythmChart
  },
  data() {
    return {
      dischargeRhythmData: {},
      dischargeRhythmChartData: {},
      hourlyOutputData: {},
      hourlyOutputChartData: {},
      millLines: ['2250', '1580'],
      curLine: '2250',
      timer: null
    }
  },
  created() {
  },
  mounted() {
    this.$nextTick(() => {
      this.initRefresh()

      if (this.timer) {
        clearInterval(this.timer)
      } else {
        this.timer = setInterval(() => {
          this.refreshBackendData()
        }, 60000)
      }
    })
  },
  methods: {
    handleSetChartData(type) {
      this.refreshChartData(type)
      this.curLine = type
    },
    refreshChartData(line) {
      this.dischargeRhythmChartData = this.dischargeRhythmData[line]
    },
    initRefresh() {
      this.refreshBackendData()
      // setTimeout(() => {
      //   this.refreshChartData(this.curLine)
      // }, 5000)
    },
    refreshBackendData() {
      // this.dischargeRhythmData = {}
      this.millLines.forEach(line => {
        this.refreshDisChargeRhythms(line)
        this.refreshHourlyOutputs(line)
      })
    },
    getQueryParam(line) {
      const endTime = dayjs()
      const endTimeString = endTime.format('YYYYMMDDHHmmss')
      const startTime = endTime.subtract(1, 'day')
      const startTimeString = startTime.format('YYYYMMDDHHmmss')
      const queryParam = {
        line: line,
        startTime: startTimeString,
        endTime: endTimeString
      }
      return queryParam
    },
    refreshDisChargeRhythms(line) {
      const queryParam = this.getQueryParam(line)
      getDischargeRhythm(queryParam).then(response => {
        console.log(response)
        this.resetDischargeRhythm(response)
        this.refreshChartData(this.curLine)
      })
    },
    resetDischargeRhythm(response) {
      const line = response.data.line

      this.dischargeRhythmData[line] = {}
      this.dischargeRhythmData[line]['xAxisData'] = response.data.xAxisData
      this.dischargeRhythmData[line]['expectedData'] = response.data.expectedData
      this.dischargeRhythmData[line]['actualData'] = response.data.actualData
    },
    refreshHourlyOutputs(line) {
      const queryParam = this.getQueryParam(line)
      getDischargeRhythm(queryParam).then(response => {
        console.log(response)
        this.resetHourlyOutput(response)
        this.refreshChartData(this.curLine)
      })
    },
    resetHourlyOutput(response) {
    }
  }
}
</script>

<style lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  position: relative;

  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

@media (max-width:1024px) {
  .chart-wrapper {
    padding: 8px;
  }
}
</style>
