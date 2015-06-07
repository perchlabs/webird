'use strict';

// A shim to replace Backbone.Wreqr with Backbone.Radio in Marionette. Requires Marionette v2.1+
// https://gist.github.com/jmeas/7992474cdb1c5672d88b
//
// From the Backbone Wreqr page
// Notice: In the next major release of Marionette, v3, Wreqr will be swapped for an updated
// library, Radio. If you've already begun using Wreqr, don't worry. This change isn't for quite
// some time: a few months, at the earliest. Also, we will support easily swapping the two libraries,
// so you won't run into any problems if you decide to continue using Wreqr.
// For an introduction to Radio, check out our blog post. As of Marionette v2.1, you can easily
// swap in Radio for Wreqr with this shim. We think you'll really like the changes!


(function(root, factory) {
  if (typeof define === 'function' && define.amd) {
    define(['backbone.marionette', 'backbone.radio', 'underscore'], function(Marionette, Radio, _) {
      return factory(Marionette, Radio, _);
  });
}
  else if (typeof exports !== 'undefined') {
    var Marionette = require('backbone.marionette');
    var Radio = require('backbone.radio');
    var _ = require('underscore');
    module.exports = factory(Marionette, Radio, _);
  }
  else {
    factory(root.Backbone.Marionette, root.Backbone.Radio, root._);
  }
}(this, function(Marionette, Radio, _) {
  'use strict';

  Marionette.Application.prototype._initChannel = function () {
    this.channelName = _.result(this, 'channelName') || 'global';
    this.channel = _.result(this, 'channel') || Radio.channel(this.channelName);
  }
}));
