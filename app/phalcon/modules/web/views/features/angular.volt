<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Angular Technology Demo')}}</title>
{{ partial('head_init') }}
  {{ stylesheet_link(['href': 'css/init_complex.css']) }}
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div hello-world></div>
      </div>
    </div>
  </div>
  {{ javascript_include(['src': 'js/init_complex.js']) }}
  {{ javascript_include(['src': 'js/angular.js']) }}
{% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
