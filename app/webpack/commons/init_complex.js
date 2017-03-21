import '../jquery-setup'
import 'bootstrap/dist/css/bootstrap'
import 'bootstrap/dist/css/bootstrap-theme'
import 'bootstrap/dist/js/bootstrap'
import init from 'init'
import locale from 'locale'
import DevelTool from 'devel-tool'

var localePromise = locale.init()
var documentPromise = new Promise(function(resolve, reject) {
  document.addEventListener('DOMContentLoaded', resolve, false)
})

init([localePromise, documentPromise])
  .then(function() {
    if (DEV) {
      window.devel = DevelTool({
        el: '#devel-tool',
        data: window.develToolData,
      })
    }
  })
