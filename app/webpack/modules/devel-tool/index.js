// System
import Vue from 'vue'
import {mapActions} from 'vuex'
// Local
import store from './store'
import DevelTool from './DevelTool'

/**
 *
 */
export default function(options) {
  const {el, data} = options

  return new Vue({

    /**
     *
     */
    el,

    /**
     *
     */
    store: store(data),

    /**
     *
     */
    render: h => h(DevelTool),

    /**
     *
     */
    methods: {

      /**
       *
       */
      ...mapActions({
        open: 'openTool',
        toggle: 'toggleTool',
      }),
    },

  })
}
