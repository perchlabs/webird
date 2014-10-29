'use strict'

angular = require('angular')

module.exports = angular.module 'app.helloworld', ['ngResource']
  .directive 'helloWorld', require('./helloWorldDirective')
  .service 'helloWorldService', require('./helloWorldService')
