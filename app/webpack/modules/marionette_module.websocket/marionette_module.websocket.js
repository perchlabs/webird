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
    this.listenTo(globalCh, 'websocket:connect', this.connect)
  },

  /**
   *
   */
  connect() {
    try {
      let loc = window.location
      let wsUri = loc.protocol === 'https:' ? 'wss:' : 'ws:'
      wsUri += "//" + loc.host + "/websocket"

      this.conn = new WebSocket(wsUri)
      this.conn.onopen = (e) => {
        globalCh.trigger('websocket:message', "Connection established!")
        this.conn.send('Hello World!')
      }
      this.conn.onmessage = (e) => {
        globalCh.trigger('websocket:message', e.data)
      }
      this.conn.onerror = (e) => {
        globalCh.trigger('websocket:message', 'Error Connecting.')
      }
    } catch (e) {
      globalCh.trigger('websocket:message', 'websocket exception')
      console.log('websocket exception', e)
    }
  }
})
