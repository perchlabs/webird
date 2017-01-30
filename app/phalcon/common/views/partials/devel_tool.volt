{%- if DEVELOPING %}
  <!--DEVEL_TOOL_START-->
  <div id="devel-tool"></div>
  <script>window.develToolData = {{devel.getData() | json_encode}}</script>
  <!--DEVEL_TOOL_END-->
{% endif %}
