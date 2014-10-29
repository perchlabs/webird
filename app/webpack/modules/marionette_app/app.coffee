'use strict'

Marionette = require 'Marionette'
Handlebars = require 'handlebars'
require './shim_backbone.radio'


module.exports = app = new Marionette.Application()

# Configure Marionette.Renderer to use Marionette instead of underscore templates
Marionette.Renderer.render = (source, data) ->
  template = Handlebars.compile source
  html = template data
