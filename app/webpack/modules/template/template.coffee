'use strict'

Handlebars = require 'handlebars'
locale = require 'locale'


Handlebars.registerHelper 'gettext', (message) ->
  locale.gettext message

Handlebars.registerHelper 't', (message) ->
  locale.gettext message

Handlebars.registerHelper 'ngettext', (msg1, msg2, n) ->
  locale.ngettext msg1, msg2, n

Handlebars.registerHelper 'n', (msg1, msg2, n) ->
  locale.ngettext msg1, msg2, n
