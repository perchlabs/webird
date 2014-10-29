'use strict'

# example simple module

module.exports =
  multiply: (a, b) -> a * b
  
  add: (a, b) -> 
  
    require.ensure ['jquery'], (require) ->
      $ = require('jquery')
      a * b
