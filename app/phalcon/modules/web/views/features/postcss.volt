<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird PostCSS Technology Demo')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/entries/postcss.example.css']) }}
</head>
<body>
  <h1>{{t('PostCSS Feature')}}</h1>
  <p>{{t('Phalcon, Webpack and PostCSS integration example:')}}</p>
  <div id="content">
      Resize this webpage for media query example
  </div>
  {{ javascript_include(['src': 'js/entries/postcss.example.js']) }}
  {% if DEVELOPING %}<!--DEBUG_PANEL-->{% endif %}
</body>
