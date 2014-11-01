  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--[if IE]><link rel="shortcut icon" href="/assets/favicon.png"><![endif]-->
  <!-- Windows 8 IE10+ "Metro" Start menu tiles 144x144 pixels -->
  <meta name="msapplication-TileColor" content="#D83434">
  <meta name="msapplication-TileImage" content="{{path}}assets/favicon_windows_start.png">
  <!-- Touch Icons - iOS and Android 2.1+ 152x152 pixels -->
  <link rel="apple-touch-icon-precomposed" href="{{path}}assets/favicon_apple_touch.png">
  <!-- Firefox, Chrome, Safari, IE 11+ and Opera 96x96 pixels -->
  <link rel="icon" href="{{path}}assets/favicon.png">
{% if ENVIRONMENT == 'dev' %}
  {# FIXME: This is causing forms to double submit in Firefox #}
  {# https://github.com/webpack/webpack-dev-server/issues/64 #}
  <!-- {{ javascript_include(['src': path~'webpack-dev-server.js']) }} -->
{% endif %}
