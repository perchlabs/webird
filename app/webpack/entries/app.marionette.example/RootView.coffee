'use strict'
# system
Backbone    = require 'backbone'
Marionette  = require 'backbone.marionette'
# app
globalCh = require 'globalCh'
# local
CounterView = require './views/CounterView'
WebsocketView = require './views/WebsocketView'
ConsoleView = require './views/ConsoleView'

Parent = Marionette.LayoutView
module.exports = Parent.extend
  template: require './partials/layout'

  regions:
    WebsocketRegion: '[data-region="websocket"]'
    CounterRegion: '[data-region="counter"]'
    ConsoleRegion: '[data-region="console"]'

  initialize: (options) ->
    @WebsocketView = new WebsocketView()

    @CounterView = new CounterView()

    @ConsoleView = new ConsoleView()
    .listenTo globalCh, 'counter:value', (counterValue) =>
      @ConsoleView.log("Counter Value #{counterValue}")

  onRender: ->
    @WebsocketRegion.show @WebsocketView
    @CounterRegion.show @CounterView
    @ConsoleRegion.show @ConsoleView
