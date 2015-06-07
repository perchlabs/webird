'use strict'
# system
Marionette = require 'Marionette'
# app
globalCh    = require 'globalCh'

Parent = Marionette.ItemView
module.exports = Parent.extend
  template: require '../partials/counter'

  events:
    'click [data-action="decrement"]': 'decrementCounter'
    'click [data-action="increment"]': 'incrementCounter'

  decrementCounter: ->
    console.log 'trigger'
    globalCh.trigger 'counter:change', -1

  incrementCounter: ->
    console.log 'trigger'
    globalCh.trigger 'counter:change', 1
