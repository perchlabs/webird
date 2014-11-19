'use strict'
# system
Marionette = require 'Marionette'
# local
Layout = require './Layout'

module.exports = Marionette.Module.extend
  startWithParent: false

  onStart: (options) ->
    @layout = new Layout

    options.region.show @layout
