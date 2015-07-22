'use strict';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import globalCh from 'globalCh';
import CounterView from './views/CounterView';
import WebsocketView from './views/WebsocketView';
import ConsoleView from './views/ConsoleView';

var Parent = Marionette.LayoutView;
module.exports = Parent.extend({
  template: require('./partials/layout'),
  regions: {
    WebsocketRegion: '[data-region="websocket"]',
    CounterRegion: '[data-region="counter"]',
    ConsoleRegion: '[data-region="console"]'
  },
  initialize: function(options) {
    this.WebsocketView = new WebsocketView();
    this.CounterView = new CounterView();

    this.ConsoleView = (new ConsoleView())
      .listenTo(globalCh, 'counter:value', (counterValue) => {
        this.ConsoleView.log(`Counter Value ${counterValue}`);
      });
  },
  onRender: function() {
    this.WebsocketRegion.show(this.WebsocketView);
    this.CounterRegion.show(this.CounterView);
    this.ConsoleRegion.show(this.ConsoleView);
  }
});
