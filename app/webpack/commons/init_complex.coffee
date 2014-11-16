'use strict'

require 'theme_style/bootstrap'
require 'theme_script/bootstrap'

init = require 'init'
template = require 'template'
locale = require 'locale'

initBlock = init.getBlockingDeferred()

# requests the locale gettext json file based on the browser locale setting
locale.init ->
  $(document).ready ->
    initBlock.resolve 'locale loading finished'

    if DEV
      debugWidget = require 'debug_panel'
      debugWidget.init()
