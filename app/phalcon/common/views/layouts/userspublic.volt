<!DOCTYPE html>
<html>
<head>
  <title>{{_('Simple Task')}}</title>
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
{{ content() }}
{% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</body>
</html>
