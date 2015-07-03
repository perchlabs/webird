<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird WebSocket Technology Demo')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <h1>{{t('Websocket Feature')}}</h1>
        <p>
          {{t('Phalcon, Webpack and Ratchet integration example:')}}
        <p>
      </div>
    </div>

    <div class="row top7">
      <div id="websocket_console" class="col-md-6">
      </div>
    </div>
  </div>
  {{ javascript_include(['src': 'js/commons/init_complex.js']) }}
  {{ javascript_include(['src': 'js/entries/websocket.js']) }}
  {% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
