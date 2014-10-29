'use strict'

# include bootstrap scripts
require 'theme_script/bootstrap'

init = require 'init'
template = require 'template'
locale = require 'locale'

initBlock = init.getBlockingDeferred()

# requests the locale gettext json file based on the browser locale setting
locale.init ->
  initBlock.resolve 'locale loading finished'
