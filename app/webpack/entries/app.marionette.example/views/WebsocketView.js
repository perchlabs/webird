import Marionette from 'backbone.marionette'
import globalCh from 'globalCh'
import template from '../partials/websocket'

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
  events: {
    'click [data-action="websocket-connect"]': 'connect'
  },

  /**
   *
   */
  connect() {
    globalCh.trigger('websocket:connect')
  }
})
