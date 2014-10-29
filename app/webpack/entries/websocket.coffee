'use strict'

appendMessage = (message) ->
  $('#websocket_console').append("<p class=\"text-info\">#{message}</p>")

appendError = (error) ->
  $('#websocket_console').append("<p class=\"text-danger\">#{error}</p>")


try
  loc = window.location
  wsUri = if loc.protocol == 'https:' then 'wss:' else 'ws:'
  wsUri += "//#{loc.host}/websocket"

  @conn = new WebSocket wsUri
  @conn.onopen = (e) =>
    appendMessage "Connection established!"
    @conn.send 'Hello World!'
  @conn.onmessage = (e) ->
    appendMessage e.data
  @conn.onerror = (e) ->
    appendError 'Error Connecting.'
catch e
  appendMessage 'websocket exception', e
