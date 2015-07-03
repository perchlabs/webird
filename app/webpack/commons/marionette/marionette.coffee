'use strict'
# system
Marionette = require 'backbone.marionette'
Radio      = require 'backbone.radio'
# app
init     = require 'init'
locale   = require 'locale'
template = require 'template'
# local
require './backbone.radio_shim'

$.ajaxSetup
  cache: false
  timeout: 3500

# TODO: Wating for nunjucks-loader to be fixed:
# Issue: https://github.com/at0g/nunjucks-loader/issues/3
# # Configure Marionette.Renderer to use Marionette instead of underscore templates
# Marionette.Renderer.render = (tpl, data) ->
#   html = tpl.render(data)
#   return html


# Configure Marionette.Renderer to use Marionette instead of underscore templates
Marionette.Renderer.render = (src, data) ->
  tpl = template.factory src
  html = tpl.render(data)
  return html

initBlock = init.getBlockingDeferred()

# TODO: Use a promise loader
# initBlock
# .then localInit
# .then documentReady
# .resolve

# requests the locale gettext json file based on the browser locale setting
locale.init ->
  $(document).ready ->
    initBlock.resolve 'locale loading finished'

    if DEV
      debugWidget = require 'debug_panel'
      debugWidget.init()
