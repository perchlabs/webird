<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Angular Technology Demo')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/commons/init_complex.css']) }}
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div hello-world></div>
      </div>
    </div>
  </div>
  {{ javascript_include(['src': 'js/commons/init_complex.js']) }}
  {{ javascript_include(['src': 'js/entries/angular.js']) }}
{% if DEVELOPING %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
