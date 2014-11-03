<!DOCTYPE html>
<html>
<head>
  <title>{{_('Webird Angular Technology Demo')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': 'css/style_bootstrap.css']) }}
</head>
<body>
  <hello-world/>
</body>
  {{ javascript_include(['src': 'js/init_complex.js']) }}
  {{ javascript_include(['src': 'js/angular.js']) }}
</html>
