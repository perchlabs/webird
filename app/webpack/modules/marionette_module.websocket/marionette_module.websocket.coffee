'use strict'
# system
Marionette = require 'Marionette'
# app
globalCh    = require 'globalCh'

module.exports = Marionette.Module.extend
  startWithParent: false

  onStart: (options) ->

    @listenTo globalCh, 'websocket:connect', @connect

  connect: ->
    try
      loc = window.location
      wsUri = if loc.protocol == 'https:' then 'wss:' else 'ws:'
      wsUri += "//#{loc.host}/websocket"
      @conn = new WebSocket wsUri

      @conn.onopen = (e) =>
        globalCh.trigger 'websocket:message', "Connection established!"
        @conn.send 'Hello World!'
      @conn.onmessage = (e) ->
        globalCh.trigger 'websocket:message', e.data
      @conn.onerror = (e) ->
        globalCh.trigger 'websocket:message', 'Error Connecting.'
    catch e
      globalCh.trigger 'websocket:message', 'websocket exception'
      console.log 'websocket exception', e
