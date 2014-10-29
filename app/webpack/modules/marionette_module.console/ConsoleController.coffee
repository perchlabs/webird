'use strict'

Backbone = require 'Backbone'
Marionette = require 'Marionette'

Layout = require './Layout'
ContentView = require './ContentView'



module.exports = Marionette.Controller.extend
  initialize: (options) ->
    # store a region that will be used to show the stuff rendered by this component
    @mainRegion = options.mainRegion

    @counter = new Backbone.Model
      count: 0

    return



  counterChange: (counterChange) ->
    count = @counter.get('count') + counterChange
    @counter.set 'count', count

    console.log 'counter', count



  # call the "show" method to get this thing on screen
  show: ->
    # get the layout and show it
    layout = @_getLayout()
    @mainRegion.show layout
    return





  # build the layout and set up a "render" event handler.
  # the event handler will set up the additional views that
  # need to be displayed in the layout. do this in "render"
  # so that the initial views are already rendered in to the
  # layout when the layout is displayed in the DOM
  _getLayout: ->
    layout = new Layout()
    @listenTo layout, "render", ->

      @_showViews layout
      return

    layout






  # render the menu and the initial content in to the layout.
  # set up an event handler so that when the menu triggers the
  # event, the content will be changed appropriately.
  _showViews: (layout) ->
    content = @_addContent layout.content

    return





  _addContent: (region) ->
    content = new ContentView
      model: @counter

    region.show content
    content
