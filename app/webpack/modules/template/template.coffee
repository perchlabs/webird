'use strict'
# system
nunjucks = require 'nunjucks/browser/nunjucks-slim'
# app
locale = require 'locale'

env = new nunjucks.Environment()
env.addGlobal 'gettext', (message) -> locale.gettext message
env.addGlobal 't', (message) -> locale.gettext message
env.addGlobal 'ngettext', (msg1, msg2, n) -> locale.ngettext msg1, msg2, n
env.addGlobal 'n', (msg1, msg2, n) -> locale.ngettext msg1, msg2, n

module.exports =
  factory: (src) -> new nunjucks.Template(src, env)
