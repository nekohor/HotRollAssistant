import request from '@/utils/request'

export function calcShiftOutput(data) {
  return request({
    url: '/outputs/shift',
    method: 'post',
    data
  })
}

