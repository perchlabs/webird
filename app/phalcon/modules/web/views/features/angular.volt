<!DOCTYPE html>
<html>
<head>
  <title>{{_('Webird Angular Technology Demo')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': path~'css/style_bootstrap.css']) }}
  {{ javascript_include(['src': path~'js/init_complex.js']) }}
  {{ javascript_include(['src': path~'js/angular.js']) }}
</head>
<body>
  <hello-world/>
</body>
</html>
