import Jed from 'jed'

/**
 *
 */
// LOCALE_ROOT is a constant defined in webpack config and is evaluated at build time
let localeConfig = require(`${LOCALE_ROOT}/config`)

/**
 *
 */
let i18n

/**
 *
 */
export default {

  /**
   *
   */
  init: function() {
    return new Promise(initExecutor)
  },

  /**
   *
   */
  gettext: function(message) {
    return i18n.gettext(message)
  },

  /**
   *
   */
  ngettext: function(msg1, msg2, n) {
    return i18n.ngettext(msg1, msg2, n)
  }
}

/**
 *
 */
function initExecutor(resolve, reject) {
  let localeDefault = localeConfig['default']
  let map           = localeConfig['map']

  let langRaw = window.navigator.userLanguage || window.navigator.language
  let langParts = langRaw.replace('-', '_').split('_')

  let language = langParts[0]
  let country = langParts.length > 1 ? '_' + langParts[1].toUpperCase() : ''
  let locale = `${language}${country}`

  let waitForLangChunk
  try {
    waitForLangChunk = getLangLoader(locale)
  } catch (eLocale) {
    let localeNext = map.hasOwnProperty(language) ? map[language] : localeDefault
    waitForLangChunk = getLangLoader(localeNext)
  }
  waitForLangChunk(function(messages) {
    i18n = new Jed(messages)
    resolve()
  })
}

/**
 *
 */
function getLangLoader(locale) {
  // An runtime exception will be throw every time that the requested locale file cannot be found.
  // Webpack uses a regular expression to build all locales as separate bundles.
  return require(`bundle!${LOCALE_ROOT}/${locale}/LC_MESSAGES/messages.po`)
}
