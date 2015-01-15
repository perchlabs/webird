'use strict'

module.exports = new class
  constructor: ->
    @requiredFeatures = []
    @cache = {}

    @test =
      canvas:     -> !!document.createElement('canvas').getContext
      video:      -> !!document.createElement('video').canPlayType
      storage:    -> window?.localStorage isnt undefined
      workers:    -> !!window.Worker
      websocket:  -> typeof(WebSocket) is 'function'
      appcache:   -> !!window.applicationCache
      geolocator: -> 'geolocation' in navigator
      history:    -> !!(window.history and history.pushState)
      intl:        -> window.Intl and typeof window.Intl is 'object'

    @support = (feature) ->
      @cache[feature] = @test[feature]() if @cache[feature] is undefined
      @cache[feature]

    @hasAllRequired = ->
      for feature in @requiredFeatures
        return false if not @support(feature)
      true

#    @supportAll = (featureArr) ->
#      for feature in featureArr
#        return false if not @support(feature)
#      true
