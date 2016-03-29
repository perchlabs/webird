import angular from 'angular'
import 'angular-ui-router'
import 'angular-cookies'
import 'angular-resource'

import angularHelloworld from 'angular_helloworld'

export default function() {
  let app = angular.module('app', [
    'ui.router',
    'ngCookies',
    'ngResource',
    angularHelloworld.name
  ])
  .constant('VERSION', VERSION)

  angular.element(document).ready(function() {
    return angular.bootstrap(document, ['app']);
  })
}
