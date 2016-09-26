'use strict'
import 'theme_style/bootstrap'
import 'theme_script/bootstrap'
import init from 'init'
import locale from 'locale'

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
