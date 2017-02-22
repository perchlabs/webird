// System
import Vue from 'vue'
import pdflib from 'pdfjs-dist'
import 'pdfjs-dist/web/pdf_viewer'
// Application
import init from 'init'
// Local
import App from './App'

// Configuration for pdf.js library
const {PDFJS}  = pdflib
// This needs to be a seperate file because it is a web worker.
// TODO: Have build system place this file.
PDFJS.workerSrc = '/static/js/pdf.worker.min.js'

/**
 *
 */
init().then(function() {
  new Vue({

    /**
     *
     */
    el: '#app',

    /**
     *
     */
    render: h => h(App),
  })
})
