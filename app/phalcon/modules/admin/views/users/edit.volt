
<div class="container">

  <div class="row">
    <div class="col-md-1">
      {{ link_to("admin/users", '&larr; ' ~ _('Go Back'), "class": "btn btn-link pull-left") }}
    </div>
  </div>

  <div class="row top17">
    <div class="col-md-6 col-md-offset-3">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#edit" data-toggle="tab">{{_('Edit')}}</a></li>
        <li><a href="#signins" data-toggle="tab">{{_('Signins')}}</a></li>
        <li><a href="#pwchanges" data-toggle="tab">{{_('Password Changes')}}</a></li>
        <li><a href="#pwresets" data-toggle="tab">{{_('Reset Passwords')}}</a></li>
      </ul>
    </div>

    <div class="col-md-12 top7">
      <div class="tabbable">
        <div class="tab-content">
          <div class="tab-pane active" id="edit">
            {% include 'users/edit/tab_pane_edit.volt' %}
          </div>
          <div class="tab-pane" id="signins">
            {% include 'users/edit/tab_pane_successful_signins.volt' %}
          </div>
          <div class="tab-pane" id="pwchanges">
            {% include 'users/edit/tab_pane_password_changes.volt' %}
          </div>
          <div class="tab-pane" id="pwresets">
            {% include 'users/edit/tab_pane_reset_passwords.volt' %}
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
