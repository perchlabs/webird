'use strict'
import $ from 'jquery'

function appendMessage(message) {
  $('#websocket_console').append("<p class=\"text-info\">" + message + "</p>")
}

function appendError(error) {
  $('#websocket_console').append("<p class=\"text-danger\">" + error + "</p>")
}

try {
  let loc = window.location
  let wsUri = loc.protocol === 'https:' ? 'wss:' : 'ws:'
  wsUri += "//" + loc.host + "/websocket"

  this.conn = new WebSocket(wsUri)
  this.conn.onopen = () => {
    appendMessage("Connection established!")
    this.conn.send('Hello World!')
  }
  this.conn.onmessage = () => {
    appendMessage(e.data)
  }
  this.conn.onerror = (e) => {
    appendError('Error Connecting.')
  }
} catch (err) {
  console.log(err)
  appendMessage('websocket exception', err)
}
