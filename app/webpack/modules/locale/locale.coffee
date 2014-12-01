'use strict'
# system
Jed = require 'jed'
# configuration
localeConfig = require "#{LOCALE_ROOT}/config"

localeDefault = localeConfig.default
map = localeConfig.map

langRaw = window.navigator.userLanguage || window.navigator.language
langParts = langRaw.replace('-', '_').split('_')
# The Locale consists of a language and country
language = langParts[0]
country = if langParts.length > 1 then '_' + langParts[1].toUpperCase() else ''
# Set full locale
locale = "#{language}#{country}"

# There is only one variable here and it Webpack expands its search using a regex
# to find all of the messages. The constant LOCALE_ROOT is evaluated at compile time
getLangLoader = (locale) ->
  bundleLoader = require "bundle!#{LOCALE_ROOT}/#{locale}/LC_MESSAGES/messages.po"
  return bundleLoader

i18n = null
module.exports =
  gettext: (message) -> i18n.gettext message
  ngettext: (msg1, msg2, n) -> i18n.ngettext msg1, msg2, n

  init: (loadApp) ->
    # Try to load the locale specified by the browser. Webpack will throw an exception
    # if it does not exist since it has been required with a regex.
    try
      waitForLangChunk = getLangLoader locale
    catch eLocale
      localeNext = if map.hasOwnProperty(language) then map[language] else localeDefault
      waitForLangChunk = getLangLoader localeNext

    waitForLangChunk (messages) ->
      console.log messages
      i18n = new Jed
        domain: 'messages'
        locale_data:
          messages: messages

      loadApp()
