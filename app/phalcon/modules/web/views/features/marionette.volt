<!DOCTYPE html>
<html>
<head>
  <title>{{_('Webird Marionette Technology Demo')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': 'css/style_bootstrap.css']) }}
</head>
<body>
  <div id="helloworld-content"></div>
  <div id="console-content"></div>
  {{ javascript_include(['src': 'js/init_complex.js']) }}
  {{ javascript_include(['src': 'js/marionette.js']) }}
</body>
