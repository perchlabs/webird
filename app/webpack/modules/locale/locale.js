'use strict';
// system
import Jed from 'jed';
// Out of webpack tree
var localeConfig = require(`${LOCALE_ROOT}/config`);

var i18n;
export default {
  init: function() {
    return new Promise(initExecutor);
  },
  gettext: function(message) {
    return i18n.gettext(message);
  },
  ngettext: function(msg1, msg2, n) {
    return i18n.ngettext(msg1, msg2, n);
  }
};

function initExecutor(resolve, reject) {
  let localeDefault = localeConfig['default'];
  let map           = localeConfig['map'];

  let langRaw = window.navigator.userLanguage || window.navigator.language;
  let langParts = langRaw.replace('-', '_').split('_');

  let language = langParts[0];
  let country = langParts.length > 1 ? '_' + langParts[1].toUpperCase() : '';
  let locale = `${language}${country}`;

  let waitForLangChunk;
  try {
    waitForLangChunk = getLangLoader(locale);
  } catch (eLocale) {
    let localeNext = map.hasOwnProperty(language) ? map[language] : localeDefault;
    waitForLangChunk = getLangLoader(localeNext);
  }
  waitForLangChunk(function(messages) {
    i18n = new Jed(messages);
    resolve();
  });
}

function getLangLoader(locale) {
  let bundleLoader = require(`bundle!${LOCALE_ROOT}/${locale}/LC_MESSAGES/messages.po`);
  return bundleLoader;
};
