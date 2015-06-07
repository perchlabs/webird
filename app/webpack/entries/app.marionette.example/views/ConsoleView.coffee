'use strict'
# system
Marionette = require 'Marionette'
# app
globalCh    = require 'globalCh'

Parent = Marionette.ItemView
module.exports = Parent.extend
  template: require '../partials/console'

  initialize: (options) ->
    @listenTo globalCh, 'websocket:message', (message) =>
      @log message
      # $('#console-log').append("<p>#{message}</p>")

  log: (message) ->
    $('#console-log').append("<p>#{message}</p>")
