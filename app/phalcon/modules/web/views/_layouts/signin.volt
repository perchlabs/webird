<!DOCTYPE html>
<html>
<head>
  <title>{{t('Signin')}}</title>
{{ common('head_init') }}
  {{ stylesheet_link(['href': 'css/entries/bootstrap.css']) }}
</head>
<body>
<div class="container">
  <div class="row">
    <div class="col-md-12 text-center">
      <img src="{{url('assets/logo.png')}}" alt="Logo"/>
    </div>
  </div>
</div>
{{ common('noscript') }}
<div id="onlywithscript" style="display:none;">
  {{ content() }}
</div>
{{common('devel_tool')}}
  {{ javascript_include(['src': 'js/entries/signin.js']) }}
</body>
</html>
