'use strict'
# system
$ = require 'jquery'

_defer = $.Deferred()
blocked = false

module.exports =
  getBlockingDeferred: ->
    if blocked
      throw new Exception 'Init blocking deferred may only be obtained once'
    blocked = true
    return _defer

  done: (callback) ->
    return _defer.promise().done callback
