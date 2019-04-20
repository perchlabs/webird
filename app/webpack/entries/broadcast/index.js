import {startWebsocket} from './websocket'
import {startServersent} from './serversent'

const elSend = document.getElementById('send')
const elMessage = document.getElementById('message')

elSend.addEventListener('click', e => {
  const message = elMessage.value
  elMessage.value = ''

  fetch('/broadcast/post', {
    method: 'POST',
    credentials: 'include',
    body: JSON.stringify({message}),
  })
})


const websocketMessagesDiv = document.getElementById('websocket-messages')
const serversentMessagesDiv = document.getElementById('serversent-messages')

startWebsocket(websocketMessagesDiv)
startServersent(serversentMessagesDiv)
