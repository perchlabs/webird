'use strict';
const gulp = require('gulp');
const webpackTasks = require('./gulpfile.webpack.js');

gulp.task('default', webpackTasks.dev);
gulp.task('webpack:dev-server', webpackTasks.dev);
gulp.task('webpack:build', webpackTasks.build);
