'use strict'
# local
helloService = require('./helloWorldService')

module.exports = ['$scope', 'helloWorldService', ($scope, helloService) ->

  $scope.msg = {}
  helloService.query().then (data) ->
    console.log data
    $scope.msg = data
]
