// System
import Vue from 'vue/dist/vue'
// Application
import init from 'init'
// Local
import App from './App'

Vue.component('app', App)

// This is transpiled to var
const ABC = 123

init().then(function() {
  const app = new Vue({
    el: '#app',
    template: '<app></app>'
  })
})
