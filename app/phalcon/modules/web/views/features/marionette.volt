<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Marionette Technology Demo')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}
</head>
<body>
  <div data-region="app"></div>
  <div id="console-content"></div>
  {{ javascript_include(['src': 'js/commons/marionette.js']) }}
  {{ javascript_include(['src': 'js/entries/app.marionette.example.js']) }}
  {% if DEVELOPING %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
