'use strict'
require(`${WEBPACK_ROOT}/bootstrap`)
import Marionette from 'backbone.marionette'
import Radio from 'backbone.radio'
import init from 'init'
import locale from 'locale'
import './backbone.radio_shim'

$.ajaxSetup({
  cache: false,
  timeout: 3500
})

Marionette.Renderer.render = function(tpl, data) {
  return tpl.render(data)
}

var localePromise = locale.init()
var documentPromise = new Promise(function(resolve, reject) {
  document.addEventListener('DOMContentLoaded', resolve, false)
})

init([localePromise, documentPromise])
  .then(function() {
    if (DEV) {
      require('debug_panel')
    }
  })
