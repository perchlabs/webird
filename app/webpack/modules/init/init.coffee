'use strict'

$ = require 'jquery'

_defer = $.Deferred()
blocked = false

module.exports =
  getBlockingDeferred: ->
    if blocked
      throw new Exception 'Deferred may only be obtained once'
    blocked = true
    return _defer

  promise: ->
    _defer.promise()
