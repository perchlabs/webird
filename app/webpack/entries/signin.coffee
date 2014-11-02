'use strict'

feature = require 'browserfeature'

hasAll = feature.hasAllRequired()

window.onload = ->
  # Only show the login form when javascript is enabled
  document.getElementById('onlywithscript')?.style.display = ''


#console.log('canvas: ', feature.support('canvas'))
#console.log('video:  ', feature.support('video'))
#console.log('storage: ', feature.support('storage'))
#console.log('workers: ', feature.support('workers'))
#console.log('websocket: ', feature.support('websocket'))
#console.log('appcache: ', feature.support('appcache'))
#console.log('geolocator: ', feature.support('geolocator'))
#console.log('history: ', feature.support('history'))
