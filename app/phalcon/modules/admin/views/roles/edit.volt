
<div class="container">

  <div class="row">
    <div class="col-sm-1 pull-left">
      {{ link_to("admin/roles", '&larr; ' ~ t('Go Back'), "class": "btn btn-link text-left") }}
    </div>
    <div class="col-sm-1 pull-right">
      {{ link_to("admin/roles/create", '<i class="glyphicon glyphicon-plus-sign"></i> ' ~ t('Create Role'), "class": "btn btn-primary text-right") }}
    </div>
  </div>

  <div class="row top17">
    <div class="col-md-6 col-md-offset-3">
      <ul class="nav nav-tabs">
        <li class="active"><a href="#edit" data-toggle="tab">{{t('Edit Role')}}</a></li>
        <li><a href="#users" data-toggle="tab">{{t('List Users')}}</a></li>
      </ul>
    </div>

    <div class="col-md-12 top7">
      <div class="tabbable">
        <div class="tab-content">
          <div class="tab-pane active" id="edit">
            {% include 'roles/edit/tab_pane_edit.volt' %}
          </div>
          <div class="tab-pane" id="users">
            {% include 'roles/edit/tab_pane_users.volt' %}
          </div>
        </div>
      </div>
    </div>


</div>
