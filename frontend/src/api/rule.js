import request from '@/utils/request'

export function getGradeCategos(query) {
  return request({
    url: '/rules/steel-grade-categos',
    method: 'get',
    params: query
  })
}
