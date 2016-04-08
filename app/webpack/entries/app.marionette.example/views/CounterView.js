import Marionette from 'backbone.marionette'
import globalCh from 'globalCh'
import template from '../partials/counter'

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
    'click [data-action="decrement"]': 'decrementCounter',
    'click [data-action="increment"]': 'incrementCounter'
  },

  /**
   *
   */
  decrementCounter() {
    console.log('trigger')
    globalCh.trigger('counter:change', -1)
  },

  /**
   *
   */
  incrementCounter() {
    console.log('trigger')
    globalCh.trigger('counter:change', 1)
  }
})
