'use strict';
var locale = require('locale');

module.exports = function(env) {
  env.addGlobal('gettext', function(message) {
    return locale.gettext(message);
  });
  env.addGlobal('t', function(message) {
    return locale.gettext(message);
  });
  env.addGlobal('ngettext', function(msg1, msg2, n) {
    return locale.ngettext(msg1, msg2, n);
  });
  env.addGlobal('n', function(msg1, msg2, n) {
    return locale.ngettext(msg1, msg2, n);
  });
};
