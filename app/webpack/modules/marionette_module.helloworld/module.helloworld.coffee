'use strict'

HelloworldController = require './HelloworldController'


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
    @ctrl = new HelloworldController
      mainRegion: options.mainRegion


  module.addFinalizer ->
#    console.log 'finalize'



  module.on 'before:start', ->
#    console.log 'before:start'


  module.on 'start', (options) ->
    @ctrl.show()
#    console.log 'start'


  module.on 'before:stop', ->
#    console.log 'before:stop'


  module.on 'stop', ->
#    console.log 'stop'



