// System
import Vue from 'vue'
import {GlobalWorkerOptions} from 'pdfjs-dist'
// Application
import init from 'commons/init_complex'
// Local
import App from './App'

// Configuration for pdf.js library
GlobalWorkerOptions.workerSrc = require('entry?name=js/pdf.worker.js!pdfjs-dist/build/pdf.worker.min.js')

init()
  .then(() => {
    new Vue({
      el: '#app',
      render: h => h(App),
    })
  })
