'use strict'

module.exports = ->
  controller: require './helloWorldCtrl'
  template: require './partials/helloWorld'
  restrict: 'A'
