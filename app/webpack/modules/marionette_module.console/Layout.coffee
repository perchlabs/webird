'use strict'
# system
Backbone   = require 'Backbone'
Marionette = require 'Marionette'
# local
ContentView = require './views/ContentView'

module.exports = Marionette.LayoutView.extend
  template: require './partials/layout'

  regions:
    contentRegion: '[data-region="content"]'

  initialize: (options) ->
    @counter = new Backbone.Model
      count: 0


  onShow: ->
    @contentRegion.show new ContentView
      model: @counter


  counterChange: (counterChange) ->
    count = @counter.get('count') + counterChange
    @counter.set 'count', count

    console.log 'counter', count
