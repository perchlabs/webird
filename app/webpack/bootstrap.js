'use strict';

console.log('HERE')

import jquery from 'jquery'
window.jQuery = jquery
window.$ = jquery

// Setup bluebird Promise polyfill for Babel
// require('babel-runtime/core-js/promise')['default'] = require('bluebird');

// Setup fetch API polyfill
require('whatwg-fetch');
