
let evtSource

export function startServersent(messagesContainer) {
  const evtSource = new EventSource('/broadcast/serversent')

  evtSource.addEventListener('open', e => {
    appendMessage(messagesContainer, 'Connection established!')
    console.log('Serversent connection established!')
  }, false)

  evtSource.addEventListener('webird', e => {
    const {message} = JSON.parse(e.data)
    const p = document.createElement('p')
    p.classList.add('text-info')
    p.textContent = message

    messagesContainer.appendChild(p)
  }, false)
}

function appendMessage(container, message) {
  const p = document.createElement('p')
  p.classList.add('text-info')
  p.textContent = message

  container.appendChild(p)
}
