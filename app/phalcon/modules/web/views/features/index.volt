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
      {{ link_to('features/angular', this.translate.t('Angular'), 'class':'btn btn-primary') }}
      {{ link_to('features/marionette', this.translate.t('Marionette'), 'class':'btn btn-primary') }}
    </div>
  </div>
  <div class="row top7">
    <div class="col-md-6">
      <h3>{{t('Technologies')}}</h3>
      {{ link_to('features/websocket', this.translate.t('WebSocket'), 'class':'btn btn-primary') }}
    </div>
  </div>
  <div class="row top7">
    <div class="col-md-6">
      <h3>{{t('Tools')}}</h3>
      {% if DEV %}
        <a href="javascript:void(0)" class="btn btn-primary" onclick="wbdebug.toggle()">{{ this.translate.t('Debug Panel') | escape }}</a>
      {% else %}
        Debug Panel is not available in dist environment
      {% endif %}
    </div>
  </div>
  {% if DEV %}<!--DEBUG_PANEL-->{% endif %}
</div>
