<!DOCTYPE html>
<html>
<head>
  <title>{{_('Simple Task')}}</title>
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
{{ content() }}
</div>
</body>
</html>
