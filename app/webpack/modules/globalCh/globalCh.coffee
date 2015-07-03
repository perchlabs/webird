'use strict'
# system
Backbone = require 'backbone'

# Simple module that gives easy access to the Marionette global channel
module.exports = Backbone.Radio.channel 'global'
