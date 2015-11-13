
<div class="container">

  <div class="row">
    <div class="col-sm-1 pull-left">
      <a href="{{ url('admin/roles') }}" class="btn btn-link text-left">&larr; {{ t('Go Back') }}</a>
    </div>
    <div class="col-sm-1 pull-right">
      <a href="{{ url('admin/roles/create') }}" class="btn btn-primary text-right"><i class="glyphicon glyphicon-plus-sign"></i> {{ t('Create Role') }}</a>
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
