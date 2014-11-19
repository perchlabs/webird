'use strict'
# system
angular = require('angular')

module.exports = angular.module 'app.helloworld', ['ngResource']
  .directive 'helloWorld', require('./helloWorldDirective')
  .service 'helloWorldService', require('./helloWorldService')
