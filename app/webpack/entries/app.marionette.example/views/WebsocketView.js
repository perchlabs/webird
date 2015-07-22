'use strict';
import Marionette from 'backbone.marionette';
import globalCh from 'globalCh';

var Parent = Marionette.ItemView;
module.exports = Parent.extend({
  template: require('../partials/websocket'),
  events: {
    'click [data-action="websocket-connect"]': 'connect'
  },
  connect: function() {
    globalCh.trigger('websocket:connect');
  }
});
