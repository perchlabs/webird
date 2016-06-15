<div class="container">
  <div class="row">
    <div class="col-md-6">
      {{ content() }}
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <h1>{{t('Webird Features')}}</h1>
      <p>{{t('This Phalcon and Webpack framework with the following features;')}}</p>
    </div>
  </div>

  <div class="row top7">
    <div class="col-md-6">
      <h3>{{t('Frameworks')}}</h3>
      <a href="{{ url('features/angular') }}" class="btn btn-primary">{{ t('Angular') }}</a>
      <a href="{{ url('features/marionette') }}" class="btn btn-primary">{{ t('Marionette') }}</a>
    </div>
  </div>
  <div class="row top7">
    <div class="col-md-6">
      <h3>{{t('Technologies')}}</h3>
      <a href="{{ url('features/websocket') }}" class="btn btn-primary">{{ t('Websocket') }}</a>
      <a href="{{ url('features/postcss') }}" class="btn btn-primary">{{ t('PostCSS') }}</a>
      <a href="{{ url('features/fetch') }}" class="btn btn-primary">{{ t('Fetch API with async/await') }}</a>
    </div>
  </div>
  <div class="row top7">
    <div class="col-md-6">
      <h3>{{t('Tools')}}</h3>
      {% if DEVELOPING %}
        <a href="javascript:void(0)" class="btn btn-primary" onclick="wbdebug.toggle()">{{ t('Debug Panel') | escape }}</a>
      {% else %}
        Debug Panel is not available in dist environment
      {% endif %}
    </div>
  </div>
  {% if DEVELOPING %}<!--DEBUG_PANEL-->{% endif %}
</div>
