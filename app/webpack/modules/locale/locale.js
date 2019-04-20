import Jed from 'jed'

let i18n
let localeConfig

export default {
  async init() {
    const localeConfig = await import(`${LOCALE_ROOT}/config`)

    const localeDefault = localeConfig['default']
    const map           = localeConfig['map']

    const langRaw = window.navigator.userLanguage || window.navigator.language
    const langParts = langRaw.replace('-', '_').split('_')

    const language = langParts[0]
    const country = langParts.length > 1 ? '_' + langParts[1].toUpperCase() : ''
    const locale = `${language}${country}`

    let localeData

    try {
      localeData = await getLocaleData(locale)
    } catch {
      const localeNext = map.hasOwnProperty(language) ? map[language] : localeDefault
      localeData = await getLocaleData(localeNext)
    }

    i18n = new Jed(localeData)
  },

  /**
   *
   */
  gettext(message) {
    return i18n.gettext(message)
  },

  /**
   *
   */
  ngettext(msg1, msg2, n) {
    return i18n.ngettext(msg1, msg2, n)
  },
}

// A runtime exception will be throw every time that the requested locale file cannot be found.
// Webpack uses a regular expression to build all locales as separate bundles.
async function getLocaleData(locale) {
  return import(`${LOCALE_ROOT}/${locale}/LC_MESSAGES/messages.po`)
}
