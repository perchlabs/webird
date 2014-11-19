'use strict'
# app
init = require 'init'
App  = require 'marionette_app'

app = new App

# We are going to name annonymous Marionette modules. This works better for
# commonjs as otherwise the module is being named twice. The annonymous Marionette
# module is delivered as a commonjs dependency.

app.module 'Helloworld', require('marionette_module.helloworld')
app.module 'Console', require('marionette_module.console')

app.addInitializer ->
  app.addRegions
    helloworldRegion: $ '#helloworld-content'
    consoleRegion:    $ '#console-content'

  @Helloworld.start
    region: @helloworldRegion

  @Console.start
    region: @consoleRegion

init.done ->
  app.start()
