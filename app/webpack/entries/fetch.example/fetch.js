'use strict'

getData()
  .then(function(data) {
    console.log('API data');
    console.log(data);
  })
  .catch(function(e) {
    console.log('Fetch Exception');
    console.log(e);
  });

async function getData() {
  let response = await fetch('/features/fetch/api', {
    'method': 'GET',
    'credentials': 'include',
    'same-origin': true,
    'no-cors': true
  });
  if (response.status !== 200) {
    throw 'Invalid status.';
  }
  if (response.headers.get('Content-Type') !== 'application/json') {
    throw 'Invalid content type.';
  }

  let json = await response.text();
  let data = JSON.parse(json);

  return data;
}
