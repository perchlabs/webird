// System
import Vue from 'vue'
import pdflib from 'pdfjs-dist'
// Application
import init from 'init'
import 'commons/vue'
// Local
import App from './App'

// Configuration for pdf.js library
const {GlobalWorkerOptions}  = pdflib
GlobalWorkerOptions.workerSrc = require('entry?name=js/pdf.worker.js!pdfjs-dist/build/pdf.worker.min.js')

init()
  .then(() => {
    new Vue({
      el: '#app',
      render: h => h(App),
    })
  })
