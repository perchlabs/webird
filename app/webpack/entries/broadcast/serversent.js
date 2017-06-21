
/**
 *
 */
let evtSource


/**
 *
 */
export function startServersent(messagesContainer) {
  const evtSource = new EventSource('/broadcast/serversent')

  evtSource.addEventListener('webird', function(e) {
    const {message} = JSON.parse(e.data)
    const p = document.createElement('p')
    p.classList.add('text-info')
    p.textContent = message

    messagesContainer.appendChild(p)
  }, false)
}
