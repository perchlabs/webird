// System
import Vue from 'vue'
// Local
import * as types from './mutation-types'

/**
 *
 */
export default {

  /**
   *
   */
  [types.SET_OPEN_FLAG] (state, payload) {
    state.isOpen = payload
    if (state.isOpen) {
      state.wasOpened = true
    }
  },

  /**
   *
   */
  [types.SET_ACTIVE_PANEL_NAME] (state, payload) {
    state.activePanelName = payload
  },

};
// NOTICE: The semicolon is necessary here due to limitations in Buble transpiler.
