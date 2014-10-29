'use strict'

Marionette = require 'Marionette'

HelloworldController = require './HelloworldController'


module.exports = Marionette.Module.extend
  startWithParent: false

  initialize: ->

#      console.log 'onStart', options



  onStart: (options) ->
    @ctrl.show()



#  onStart: (options) ->
#    @ctrl = new HelloworldController
#      mainRegion: options.region
#      message: 'Helloworld'


#    console.log 'onStart', options

#    @ctrl.show()




  onStop: (options) ->
    conosle.log 'onStop', options
