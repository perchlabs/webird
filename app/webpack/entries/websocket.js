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

  let conn = new WebSocket(wsUri)
  conn.addEventListener('open', function(e) {
    appendMessage("Connection established!")
    conn.send('Hello World!')
  })
  conn.addEventListener('message', function(e) {
    appendMessage(e.data)
  })
  conn.addEventListener('error', function(e) {
    appendError('Error Connecting.')
  })
} catch (err) {
  console.log(err)
  appendMessage('websocket exception', err)
}
