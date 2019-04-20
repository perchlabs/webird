// System
import Vue from 'vue'
import pdflib from 'pdfjs-dist'
import 'pdfjs-dist/web/pdf_viewer'
// Application
import init from 'init'
import 'commons/vue'
// Local
import App from './App'

// Configuration for pdf.js library
const {PDFJS}  = pdflib
PDFJS.workerSrc = require('entry?name=js/pdf.worker.js!pdfjs-dist/build/pdf.worker.min.js')

init()
  .then(() => {
    new Vue({
      el: '#app',
      render: h => h(App),
    })
  })
