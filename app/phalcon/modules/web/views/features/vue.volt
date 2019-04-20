<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Vue Technology Demo')}}</title>
  {{ common('head_init') }}
  {#{{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}#}
</head>
<body>
  <div id="app"></div>
{{common('devel_tool')}}
  {{ javascript_include(['src': 'js/entries/vue.simple.js']) }}
</body>
</html>
