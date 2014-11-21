'use strict'

module.exports = ['$http', ($http) ->

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
