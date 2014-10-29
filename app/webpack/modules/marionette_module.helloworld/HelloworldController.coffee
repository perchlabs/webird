'use strict'

Backbone = require 'Backbone'
Marionette = require 'Marionette'

globalCh = require 'globalCh'
Layout = require './Layout'
HeaderView     = require './HeaderView'
ContentView = require './ContentView'



module.exports = Marionette.Controller.extend
  initialize: (options) ->

    @message = new Backbone.Model
      message: 'Hello World'

    # store a region that will be used to show the stuff rendered by this component
    @mainRegion = options.mainRegion
    return




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
    header = @_addHeader layout.header
    content = @_addContent layout.content

    return






  _addHeader: (region) ->
    header = new HeaderView
      model: @message

    region.show header
    header






  _addContent: (region) ->
    content = new ContentView
      model: @message
    .on 'decrement', =>
      globalCh.trigger 'counter:change', -1
    .on 'increment', =>
      globalCh.trigger 'counter:change', 1

    region.show content
    content
