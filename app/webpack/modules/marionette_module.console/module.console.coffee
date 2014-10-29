'use strict'

globalCh = require 'globalCh'

ConsoleController = require './ConsoleController'


# Note: The outer module is for commonjs and the module parameter is the Marionette.Module object
module.exports = (module, app) ->
# You can use any other following styles to access the current Marionette module. The one that accesses the module
# parameter is the most efficient because the parameter can be minimized to a single character
# ex;
#  @.startWithParent = false
#  this.startWithParent = false
#  module.startWithParent = false

  module.startWithParent = false


  module.addInitializer (options) ->
    @ctrl = new ConsoleController
      mainRegion: options.mainRegion
    .listenTo globalCh, 'counter:change', (counterChange) ->
      @counterChange counterChange


  module.on 'start', (options) ->
    @ctrl.show()
