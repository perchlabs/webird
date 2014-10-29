'use strict'

Marionette = require 'Marionette'

module.exports = Marionette.ItemView.extend
  template: require './partials/content'

  initialize: ->
    @listenTo @model, 'change', @render
