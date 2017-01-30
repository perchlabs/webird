// Local
import * as types from './mutation-types'

/**
 *  mutations are operations that actually mutates the state.
 *  each mutation handler gets the entire state tree as the
 *  first argument, followed by additional payload arguments.
 *  mutations must be synchronous and can be recorded by plugins
 *  for debugging purposes.
 */
export default {

  /**
   *
   */
  [types.INCREMENT] (state) {
    state.count++
  },

  /**
   *
   */
  [types.DECREMENT] (state) {
    state.count--
  },
};
// NOTICE: The semicolon is necessary here due to limitations in Buble transpiler.
