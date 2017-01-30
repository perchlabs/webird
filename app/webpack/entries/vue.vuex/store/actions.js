// Local
import * as types from './mutation-types'

/**
 *  actions are functions that causes side effects and can involve asynchronous operations.
 *
 */
export default {

  /**
   *
   */
  increment: ({commit}) => commit(types.INCREMENT),

  /**
   *
   */
  decrement: ({commit}) => commit(types.DECREMENT),

  /**
   *
   */
  incrementIfOdd ({commit, state}) {
    if ((state.count + 1) % 2 === 0) {
      commit(types.INCREMENT)
    }
  },

  /**
   *
   */
  incrementAsync ({commit}) {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        commit(types.INCREMENT)
        resolve()
      }, 1000)
    })
  },
}
