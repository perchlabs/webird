'use strict'
# system
Marionette = require 'backbone.marionette'
# app
globalCh = require 'globalCh'

module.exports = Marionette.Module.extend
  startWithParent: false

  onStart: (options) ->
    @counter = 0

    @listenTo globalCh, 'counter:change', (changeAmount) ->


      @counter += changeAmount
      globalCh.trigger 'counter:value', @counter




  onStop: (options) ->
