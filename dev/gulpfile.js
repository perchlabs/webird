'use strict';
var gulp = require('gulp');

require('./gulpfile.webpack.js');

gulp.task('default', ['webpack:dev-server']);
