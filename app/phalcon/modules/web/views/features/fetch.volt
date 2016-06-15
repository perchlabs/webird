<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Fetch API Technology Demo')}}</title>
{{ common('head_init') }}
</head>
<body>
  <h1>{{t('Fetch API Feature')}}</h1>
  <p>{{t('Phalcon, Webpack and Fetch API with async/await integration example:')}}</p>
  <div id="content">
      View the console to see the Fetch data
  </div>
  {{ javascript_include(['src': 'js/entries/fetch.example.js']) }}
  {% if DEVELOPING %}<!--DEBUG_PANEL-->{% endif %}
</body>
