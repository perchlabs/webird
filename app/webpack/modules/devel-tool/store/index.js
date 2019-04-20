// System
import Vue from 'vue'
import Vuex from 'vuex'
// Local
import state from './state'
import mutations from './mutations'
import getters from './getters'
import actions from './actions'

Vue.use(Vuex)

export default function(data) {
  return new Vuex.Store({
    mutations,
    getters,
    actions,
    state: state(data),
    strict: false,
  })
}
