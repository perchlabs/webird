'use strict'
# system
Marionette = require 'Marionette'
# app
template = require 'template'
# local
require './shim_backbone.radio'

# Configure Marionette.Renderer to use Marionette instead of underscore templates
Marionette.Renderer.render = (src, data) ->
  tpl = template.factory src
  html = tpl.render(data)
  return html

module.exports = Marionette.Application
