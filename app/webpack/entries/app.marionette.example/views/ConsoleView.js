'use strict';
import Marionette from 'backbone.marionette';
import globalCh from 'globalCh';

var Parent = Marionette.ItemView;
module.exports = Parent.extend({
  template: require('../partials/console'),
  initialize: function(options) {
    this.listenTo(globalCh, 'websocket:message', (message) => {
        this.log(message);
    });
  },
  log: function(message) {
    $('#console-log').append("<p>" + message + "</p>");
  }
});
