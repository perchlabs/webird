
let conn

export function startWebsocket(messagesContainer) {
  try {
    const {location} = window
    const {host, protocol} = location
    let wsUri = protocol === 'https:' ? 'wss:' : 'ws:'
    wsUri += `//${host}/websocket`

    conn = new WebSocket(wsUri)
    conn.addEventListener('open', function(e) {
      appendMessage(messagesContainer, 'Connection established!')
      console.log('Websocket connection established!')
    })
    conn.addEventListener('message', function(e) {
      appendMessage(messagesContainer, e.data)
    })
    conn.addEventListener('error', function(e) {
      alert('Error Connecting.')
    })
  } catch (err) {
    alert('websocket exception')
  }
}

function appendMessage(container, message) {
  const p = document.createElement('p')
  p.classList.add('text-info')
  p.textContent = message

  container.appendChild(p)
}
