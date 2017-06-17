
const messages = document.getElementById('messages')
const evtSource = new EventSource('serversent/messages')

evtSource.addEventListener("webird", function(e) {
  const {count} = JSON.parse(e.data)

  const newElement = document.createElement('li')
  newElement.innerHTML = count
  messages.appendChild(newElement)
}, false)
