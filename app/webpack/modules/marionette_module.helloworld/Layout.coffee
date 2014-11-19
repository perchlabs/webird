'use strict'
# system
Backbone    = require 'Backbone'
Marionette  = require 'Marionette'
# app
globalCh    = require 'globalCh'
# local
HeaderView  = require './views/HeaderView'
ContentView = require './views/ContentView'


module.exports = Marionette.LayoutView.extend
  template: require './partials/layout'

  regions:
    headerRegion: '[data-region="header"]'
    contentRegion: '[data-region="content"]'


  initialize: (options) ->
    @message = new Backbone.Model
      message: 'Hello World'


  onShow: ->
    @header = new HeaderView
      model: @message

    @content = new ContentView
      model: @message
    .on 'decrement', =>
      globalCh.trigger 'counter:change', -1
    .on 'increment', =>
      globalCh.trigger 'counter:change', 1


    @headerRegion.show @header
    @contentRegion.show @content
