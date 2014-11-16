  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--[if IE]><link rel="shortcut icon" href="{{url('assets/favicon.png')}}"><![endif]-->
  <!-- Windows 8 IE10+ "Metro" Start menu tiles 144x144 pixels -->
  <meta name="msapplication-TileColor" content="#D83434">
  <meta name="msapplication-TileImage" content="{{url('assets/favicon_windows_start.png')}}">
  <!-- Touch Icons - iOS and Android 2.1+ 152x152 pixels -->
  <link rel="apple-touch-icon-precomposed" href="{{url('assets/favicon_apple_touch.png')}}">
  <!-- Firefox, Chrome, Safari, IE 11+ and Opera 96x96 pixels -->
  <link rel="icon" href="{{url('assets/favicon.png')}}">
{% if DEV and config.dev.webpackLiveReload %}
  {{ javascript_include(['src': 'http://localhost:'~config.dev.webpackPort~'/webpack-dev-server.js'], true) }}
{% endif %}
