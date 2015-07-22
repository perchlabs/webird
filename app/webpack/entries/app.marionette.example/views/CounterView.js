'use strict';
import Marionette from 'backbone.marionette';
import globalCh from 'globalCh';

var Parent = Marionette.ItemView;
module.exports = Parent.extend({
  template: require('../partials/counter'),
  events: {
    'click [data-action="decrement"]': 'decrementCounter',
    'click [data-action="increment"]': 'incrementCounter'
  },
  decrementCounter: function() {
    console.log('trigger');
    globalCh.trigger('counter:change', -1);
  },
  incrementCounter: function() {
    console.log('trigger');
    globalCh.trigger('counter:change', 1);
  }
});
