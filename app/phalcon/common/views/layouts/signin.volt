<!DOCTYPE html>
<html>
<head>
  <title>{{_('Signin')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': path~'css/style_bootstrap.css']) }}
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-12 text-center">
      <img src="/assets/logo.png" alt="Logo"/>
    </div>
  </div>
</div>
{{ partial('noscript') }}
<div id="onlywithscript" style="display:none;">
  {{ content() }}
</div>
  {{ javascript_include(['src': path~'js/init_complex.js']) }}
  {{ javascript_include(['src': path~'js/signin.js']) }}
</body>
</html>
