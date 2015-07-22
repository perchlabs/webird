'use strict';
// System
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
// Application
import init from 'init';
import WebsocketModule from 'marionette_module.websocket';
import CounterModule from 'marionette_module.counter';
// Local
import RootView from './RootView';

var app = new Marionette.Application({
  initialize: function() {
    this.starting = true;
    this.module('Websocket', WebsocketModule);
    this.module('Counter', CounterModule);
    this.rootView = new RootView({
      el: '[data-region="app"]'
    });
  }
});

app.on('start', function(options) {
  this.rootView.render();
  this.Websocket.start();
  this.Counter.start();
});

init().then(function() {
  app.start();
});
