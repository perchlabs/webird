'use strict'

Marionette = require 'Marionette'

module.exports = Marionette.LayoutView.extend
  template: require './partials/layout'

  regions:
    content: '#content'

  initialize: (options) ->

  onShow: ->
