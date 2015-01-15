<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Marionette Technology Demo')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': 'css/init_complex.css']) }}
</head>
<body>
  <div id="helloworld-content"></div>
  <div id="console-content"></div>
  {{ javascript_include(['src': 'js/init_complex.js']) }}
  {{ javascript_include(['src': 'js/marionette.js']) }}
  {% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
