// Local
import * as types from './mutation-types'

/**
 *
 */
export default {

  /**
   *
   */
  openTool({commit}) {
    commit(types.SET_OPEN_FLAG, true)
  },

  /**
   *
   */
  closeTool({commit}) {
    commit(types.SET_OPEN_FLAG, false)
  },

  /**
   *
   */
  toggleTool({getters, commit}) {
    commit(types.SET_OPEN_FLAG, !getters.isOpen)
  },

  /**
   *
   */
  setActivePanel({getters, commit}, payload) {
    const panelName = payload

    commit(types.SET_ACTIVE_PANEL_NAME, panelName)
  },
}
