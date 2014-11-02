'use strict'

init = require 'init'
app = require 'marionette_app'

app.addRegions
  helloworldRegion: $ '#helloworld-content'
  consoleRegion:    $ '#console-content'

# Here we are going to name annonymous Marionette modules. This works better for commonjs
# as otherwise the module is being named twice. The annonymous Marionette module is
# delivered as a commonjs dependency.
app.module 'Helloworld', require('marionette_module.helloworld')
app.module 'Console', require('marionette_module.console')

app.addInitializer ->
  @Helloworld.start
    mainRegion: @helloworldRegion

  @Console.start
    mainRegion: @consoleRegion

init.done ->
  app.start()
