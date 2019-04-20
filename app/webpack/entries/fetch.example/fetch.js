
// You must wrap your async code within an async function.
// Async code cannot be called directly in non-async.
doFetch()

async function doFetch() {
  let response

  try {
    response = await fetch('/features/fetch/api', {
      'method': 'GET',
      'credentials': 'include',
      'same-origin': true,
      'no-cors': true,
    })
  } catch (e) {
    console.log('Fetch Exception')
    console.log(e)
  }

  if (response.status !== 200) {
    throw 'Invalid status.'
  }

  const data = await response.json()
  console.log('API data')
  console.log(data)
}
