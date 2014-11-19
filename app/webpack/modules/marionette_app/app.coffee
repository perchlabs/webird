'use strict'
# system
Marionette = require 'Marionette'
Handlebars = require 'handlebars'
# local
require './shim_backbone.radio'

# Configure Marionette.Renderer to use Marionette instead of underscore templates
Marionette.Renderer.render = (source, data) ->
  template = Handlebars.compile source
  html = template data

module.exports = Marionette.Application
