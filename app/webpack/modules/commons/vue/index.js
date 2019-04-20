// System
// Application
import init from 'init'
import locale from 'locale'
import DevelTool from 'devel-tool'

const localePromise = locale.init()
const documentPromise = new Promise(function(resolve, reject) {
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
