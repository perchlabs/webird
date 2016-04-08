import Marionette from 'backbone.marionette'
import globalCh from 'globalCh'

/**
 *
 */
export default Marionette.Module.extend({

  /**
   *
   */
  startWithParent: false,

  /**
   *
   */
  onStart(options) {
    this.counter = 0

    this.listenTo(globalCh, 'counter:change', function(changeAmount) {
      this.counter += changeAmount
      globalCh.trigger('counter:value', this.counter)
    })
  },

  /**
   *
   */
  onStop(options) {
  }
})
