'use strict';
// System
import Jed from 'jed';

// configuration
var localeConfig = require(LOCALE_ROOT + "/config");

var localeDefault = localeConfig["default"];
var map = localeConfig.map;

var langRaw = window.navigator.userLanguage || window.navigator.language;
var langParts = langRaw.replace('-', '_').split('_');
// The Locale consists of a language and country
var language = langParts[0];
var country = langParts.length > 1 ? '_' + langParts[1].toUpperCase() : '';
// Set full locale
var locale = `${language}${country}`;

function getLangLoader(locale) {
    let bundleLoader = require("bundle!" + LOCALE_ROOT + "/" + locale + "/LC_MESSAGES/messages.po");
    return bundleLoader;
};

var i18n;
module.exports = {
    gettext: function(message) {
        return i18n.gettext(message);
    },
    ngettext: function(msg1, msg2, n) {
        return i18n.ngettext(msg1, msg2, n);
    },
    init: function(loadApp) {
        var waitForLangChunk;
        try {
            waitForLangChunk = getLangLoader(locale);
        } catch (eLocale) {
            let localeNext = map.hasOwnProperty(language) ? map[language] : localeDefault;
            waitForLangChunk = getLangLoader(localeNext);
        }
        waitForLangChunk(function(messages) {
            // console.log(messages);
            i18n = new Jed(messages);
            loadApp();
        });
    }
};
