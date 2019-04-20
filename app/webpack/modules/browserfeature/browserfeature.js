let cache = {}

let test = {
  canvas() {
    return !!document.createElement('canvas').getContext
  },
  video() {
    return !!document.createElement('video').canPlayType
  },
  storage() {
    return (typeof window !== "undefined" && window !== null ? window.localStorage : void 0) !== void 0
  },
  workers() {
    return !!window.Worker
  },
  websocket() {
    return typeof WebSocket === 'function'
  },
  appcache() {
    return !!window.applicationCache
  },
  geolocator() {
    return indexOf.call(navigator, 'geolocation') >= 0
  },
  history() {
    return !!(window.history && history.pushState)
  },
  intl() {
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
