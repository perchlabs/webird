import '../../bootstrap'
// System
import Vue from 'vue'
import VueResource from 'vue-resource'
// Application
import init from 'init'
import locale from 'locale'

Vue.use(VueResource)

const localePromise = locale.init()
const documentPromise = new Promise(function(resolve, reject) {
  document.addEventListener('DOMContentLoaded', resolve, false)
})

init([localePromise, documentPromise])
  .then(function() {
    if (DEV) {
      require('debug_panel')
    }
  })
