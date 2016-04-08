'use strict'
import Marionette from 'backbone.marionette'
import globalCh from 'globalCh'
import template from '../partials/console'

/**
 *
 */
let Parent = Marionette.ItemView
export default Parent.extend({

  /**
   *
   */
  template,

  /**
   *
   */
  initialize(options) {
    this.listenTo(globalCh, 'websocket:message', (message) => {
      this.log(message)
    })
  },

  /**
   *
   */
  log(message) {
    $('#console-log').append("<p>" + message + "</p>")
  }
})
