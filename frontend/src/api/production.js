import request from '@/utils/request'

export function calcShiftOutput(data) {
  return request({
    url: '/outputs/shift',
    method: 'post',
    data
  })
}

export function getDischargeRhythm(data) {
  return request({
    url: '/rhythms/discharge',
    method: 'get',
    params: data
  })
}

