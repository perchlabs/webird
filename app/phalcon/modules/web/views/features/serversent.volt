<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Server-Sent Technology Demo')}}</title>
{{ common('head_init') }}
</head>
<body>
  <h1>{{t('Server-Sent Feature')}}</h1>
  <p>{{t('Phalcon, Webpack and Server-Sent API:')}}</p>
  <div id="messages"></div>

{{common('devel_tool')}}
  {{ javascript_include(['src': 'js/entries/server-sent.js']) }}
</body>
