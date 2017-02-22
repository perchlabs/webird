<!DOCTYPE html>
<html>
<head>
  <title>{{t('Webird Pdf Viewer Technology Demo')}}</title>
  {{ common('head_init') }}
</head>
<body>
  <div id="app"></div>
{{common('devel_tool')}}
  {{ javascript_include(['src': 'js/commons/vue.js']) }}
  {{ javascript_include(['src': 'js/entries/pdf-viewer.js']) }}
</body>
</html>
