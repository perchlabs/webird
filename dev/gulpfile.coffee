'use strict'

gulp = require 'gulp'

require './gulpfile.webpack.coffee'

# Default task
gulp.task 'default', ['webpack:dev-server']
