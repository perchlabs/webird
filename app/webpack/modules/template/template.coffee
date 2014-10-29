'use strict'

Handlebars = require 'handlebars'
locale = require 'locale'

Handlebars.registerHelper '_t', (message) ->
  locale.gettext message

# TODO: Get this working
Handlebars.registerHelper '_n', (msg1, msg2, n) ->
  locale.ngettext msg1, msg2, n
