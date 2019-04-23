// System
import Vue from 'vue'
// Application
import init from 'commons/init_complex'
// Local
import App from './App'

init()
  .then(() => {
    new Vue({
      el: '#app',
      render: h => h(App),
    })
  })
