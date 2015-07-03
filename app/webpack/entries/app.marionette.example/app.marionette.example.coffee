'use strict'
# system
Backbone = require 'backbone'
Marionette = require 'backbone.marionette'
# app
init = require 'init'
# local
RootView = require './RootView'

app = new Marionette.Application
  initialize: ->
    @starting = true

    @module 'Websocket', require('marionette_module.websocket')
    @module 'Counter', require('marionette_module.counter')

    # Root View
    @rootView = new RootView
      el: '[data-region="app"]'

    null

app.on 'start', (options) ->
  @rootView.render()

  @Websocket.start()
  @Counter.start()

init.done ->
  app.start()
