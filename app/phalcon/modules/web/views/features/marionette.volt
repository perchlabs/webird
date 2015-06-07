<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Marionette Technology Demo')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/init_complex.css']) }}
</head>
<body>
  <div data-region="app"></div>
  <div id="console-content"></div>
  {{ javascript_include(['src': 'js/marionette.js']) }}
  {{ javascript_include(['src': 'js/app.marionette.example.js']) }}
  {% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
