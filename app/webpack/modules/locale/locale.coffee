'use strict'

Jed = require 'jed'

langRaw = window.navigator.userLanguage || window.navigator.language
langParts = langRaw.replace('-', '_').split('_')
multipart = (langParts.length > 1)
# The Locale consists of a language and territory
language = langParts[0]
territory = if multipart then langParts[1].toUpperCase()
# Set full
locale = if multipart then "#{language}_#{territory}" else language


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
    # if it does not exist since it has been required with a regex. Then if the
    # locale has both both parts then try to load the base language without a territory
    # code (ex. 'es', 'en').  If this fails then load the default language (usually 'en_US').
    # If the locale is not multipart then just fallback to the default language.
    # This allows for a single base language to be used without territories or
    # with incomplete coverage for all territories.
    try
      waitForLangChunk = getLangLoader locale
    catch eLocale
      if multipart
        try
          waitForLangChunk = getLangLoader language
        catch eLanguage
          waitForLangChunk = getLangLoader LOCALE_DEFAULT
      else
        waitForLangChunk = getLangLoader LOCALE_DEFAULT

    waitForLangChunk (messages) ->
      i18n = new Jed
        domain: 'messages'
        locale_data:
          messages: messages

      console.log 'messages:', messages
      loadApp()
