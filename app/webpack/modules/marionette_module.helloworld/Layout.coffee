'use strict'

Marionette = require 'Marionette'

module.exports = Marionette.LayoutView.extend
  template: require './partials/layout'

  regions:
    header: '[data-region="header"]'
    content: '[data-region="content"]'


  initialize: (options) ->
    # console.log 'ContentLayout', options

  onShow: ->
