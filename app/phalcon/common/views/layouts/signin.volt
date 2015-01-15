<!DOCTYPE html>
<html>
<head>
  <title>{{t('Signin')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': 'css/init_complex.css']) }}
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-12 text-center">
      <img src="{{url('assets/logo.png')}}" alt="Logo"/>
    </div>
  </div>
</div>
{{ partial('noscript') }}
<div id="onlywithscript" style="display:none;">
  {{ content() }}
</div>
  {{ javascript_include(['src': 'js/init_complex.js']) }}
  {{ javascript_include(['src': 'js/signin.js']) }}
  {% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
