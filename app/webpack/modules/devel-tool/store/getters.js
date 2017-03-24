
/**
 *
 */
export default {

  /**
   *
   */
  wasOpened(state) {
    return state.wasOpened
  },

  /**
   *
   */
  isOpen(state) {
    return state.isOpen
  },

  /**
   *
   */
  isPanelOpen(state) {
    return (state.activePanelName !== false)
  },

  /**
   *
   */
  activePanelName(state) {
    return state.activePanelName
  },

  /**
   *
   */
  panelData(state) {
    return state.data.panels
  },

  /**
   *
   */
  measurement(state) {
    return state.data.measurement
  },

}
