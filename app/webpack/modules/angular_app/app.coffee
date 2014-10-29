'use strict'

angular = require('angular')
require('angular-ui-router')
require('angular-cookies')
require('angular-resource')


# Bootstrap Angular
app = angular.module 'app', [
  'ui.router'
  'ngCookies'
  'ngResource'
  require('angular_helloworld').name
]
  .constant('VERSION', VERSION)

# Delay the bootstrapping until window has loaded
angular.element(document).ready -> 
  angular.bootstrap(document, ['app'])

# Export the module out of this code block
module.exports = app
