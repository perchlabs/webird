'use strict'

module.exports = ['$http', ($http) ->
  console.log 'service'

  query = ->
    $http
      method: 'GET',
      url: '/api/helloworld'
    .then (response) ->
      return response.data

  return {
    query: query
  }
]
