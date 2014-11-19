'use strict'

Marionette = require 'Marionette'

module.exports = Marionette.ItemView.extend
  template: require '../partials/content'

  events:
    'click [data-action="decrement"]': 'decrementCounter'
    'click [data-action="increment"]': 'incrementCounter'


  decrementCounter: ->
    @trigger 'decrement'



  incrementCounter: ->
    @trigger 'increment'
