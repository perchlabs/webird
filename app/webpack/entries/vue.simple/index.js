// System
import Vue from 'vue'
// Application
import init from 'init'
import 'commons/vue'
// Local
import App from './App'

init().then(() => {
  new Vue({
    el: '#app',
    render: h => h(App),
  })
})
