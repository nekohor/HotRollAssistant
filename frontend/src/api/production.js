import request from '@/utils/request'

export function calcYield(data) {
  return request({
    url: '/production/yield/shift',
    method: 'post',
    data
  })
}

export function gradeCategos(query) {
  return request({
    url: '/grade/categos',
    method: 'get',
    params: query
  })
}
