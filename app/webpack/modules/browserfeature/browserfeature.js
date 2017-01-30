let cache = {}

let test = {
  canvas: function() {
    return !!document.createElement('canvas').getContext
  },
  video: function() {
    return !!document.createElement('video').canPlayType
  },
  storage: function() {
    return (typeof window !== "undefined" && window !== null ? window.localStorage : void 0) !== void 0
  },
  workers: function() {
    return !!window.Worker
  },
  websocket: function() {
    return typeof WebSocket === 'function'
  },
  appcache: function() {
    return !!window.applicationCache
  },
  geolocator: function() {
    return indexOf.call(navigator, 'geolocation') >= 0
  },
  history: function() {
    return !!(window.history && history.pushState)
  },
  intl: function() {
    return window.Intl && typeof window.Intl === 'object'
  },
}

export default {
  support: function(feature) {
    if (cache[feature] === void 0) {
      cache[feature] = test[feature]()
    }
    return cache[feature]
  }
}
