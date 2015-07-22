'use strict';

// Setup bluebird Promise polyfill for Babel
require('babel-runtime/core-js/promise')['default'] = require('bluebird');

// https://github.com/github/fetch
require('fetch-polyfill');
