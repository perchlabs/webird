// System
import Vue from 'vue'
// Local
import Counter from './Counter.vue'
import store from './store'

new Vue({
  el: '#app',
  store,
  render: h => h(Counter),
})
