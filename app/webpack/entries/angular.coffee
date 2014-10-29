'use strict'

init = require 'init'

init.promise().done (message) ->
  app = require 'angular_app'
