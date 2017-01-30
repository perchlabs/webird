// System
import Vue from 'vue/dist/vue'
// Application
import init from 'init'
// Local
import App from './App'

/**
 *
 */
init().then(function() {
  new Vue({
    el: '#app',
    render: h => h(App),
  })
})
