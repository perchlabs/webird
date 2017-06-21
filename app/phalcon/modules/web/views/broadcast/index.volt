<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird WebSocket Technology Demo')}}</title>
{{ common('head_init') }}
  {#{{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}#}
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        {{content()}}
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 text-center">
        <h2>{{t('Broadcast')}}</h2>
        <input id="message" class="form-control"/>
        <button id="send" class="btn btn-primary">send</button>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 text-center">
        <h3>Websockets</h3>
        <div id="websocket-messages"></div>
      </div>
      <div class="col-md-6 text-center">
        <h3>Server-sent</h3>
        <div id="serversent-messages"></div>
      </div>
    </div>

  </div>
  {{ javascript_include(['src': 'js/entries/broadcast.js']) }}
</body>
</html>
