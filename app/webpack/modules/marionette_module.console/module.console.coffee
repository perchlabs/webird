'use strict'
# system
Marionette = require 'Marionette'
# app
globalCh = require 'globalCh'
# local
Layout = require './Layout'

module.exports = Marionette.Module.extend
  startWithParent: false

  onStart: (options) ->
    @layout = new Layout()
    .listenTo globalCh, 'counter:change', (counterChange) ->
      @counterChange counterChange

    options.region.show @layout


  onStop: (options) ->
