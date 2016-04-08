import Backbone from 'backbone'
import Marionette from 'backbone.marionette'
import globalCh from 'globalCh'
import CounterView from './views/CounterView'
import WebsocketView from './views/WebsocketView'
import ConsoleView from './views/ConsoleView'
import template from './partials/layout'

/**
 *
 */
let Parent = Marionette.LayoutView
export default Parent.extend({

  /**
   *
   */
  template,

  /**
   *
   */
  regions: {
    WebsocketRegion: '[data-region="websocket"]',
    CounterRegion: '[data-region="counter"]',
    ConsoleRegion: '[data-region="console"]'
  },

  /**
   *
   */
  initialize(options) {
    this.WebsocketView = new WebsocketView()
    this.CounterView = new CounterView()

    this.ConsoleView = (new ConsoleView())
      .listenTo(globalCh, 'counter:value', (counterValue) => {
        this.ConsoleView.log(`Counter Value ${counterValue}`)
      })
  },

  /**
   *
   */
  onRender() {
    this.WebsocketRegion.show(this.WebsocketView)
    this.CounterRegion.show(this.CounterView)
    this.ConsoleRegion.show(this.ConsoleView)
  }
})
