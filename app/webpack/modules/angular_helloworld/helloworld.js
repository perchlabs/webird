import angular from 'angular'
import helloWorldDirective from './helloWorldDirective'
import helloWorldService from './helloWorldService'

export default angular.module('app.helloworld', ['ngResource'])
  .directive('helloWorld', helloWorldDirective)
  .service('helloWorldService', helloWorldService)
