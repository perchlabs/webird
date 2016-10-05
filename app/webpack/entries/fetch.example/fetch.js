
fetch('/features/fetch/api', {
  'method': 'GET',
  'credentials': 'include',
  'same-origin': true,
  'no-cors': true
})
.then(function(response) {
  if (response.status !== 200) {
    throw 'Invalid status.'
  }
  if (response.headers.get('Content-Type') !== 'application/json') {
    throw 'Invalid content type.'
  }
  return response.text()
})
.then(function(data) {
  console.log('API data')
  console.log(data)
})
.catch(function(e) {
  console.log('Fetch Exception')
  console.log(e)
})
