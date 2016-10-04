<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Vue Technology Demo')}}</title>
  {{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}
</head>
<body>
  <div id="app"></div>

  {{ javascript_include(['src': 'js/commons/vue.js']) }}
  {{ javascript_include(['src': 'js/entries/vue.example.js']) }}
</body>
</html>
