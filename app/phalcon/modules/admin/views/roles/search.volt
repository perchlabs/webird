{{ content() }}

<div class="container">

  <div class="row">
    <div class="col-md-1 text-left">
      <a href="{{ url('admin/roles') }}" class="btn btn-link">&larr; {{ t('Go Back') }}</a>
    </div>
    <div class="col-md-1 col-md-offset-10 text-right">
      <a href="{{ url('admin/roles/create') }}" class="btn btn-primary">{{ t('Create roles') }}</a>
    </div>
  </div>

  <div class="row top10">
    <div class="col-md-12">
      <div class="table-responsive">

        {% for role in page.items %}
        {% if loop.first %}
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>{{t('Id')}}</th>
              <th>{{t('Name')}}</th>
              <th>{{t('Active?')}}</th>
            </tr>
          </thead>
        {% endif %}
          <tbody>
            <tr>
              <td>{{ role.id }}</td>
              <td>{{ role.name }}</td>
              <td>{{ role.active == 'Y' ? t('Yes') : t('No') }}</td>
              <td width="12%">
                <a href="{{ url('admin/roles/edit/' ~ role.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-pencil"></i> {{ t('Edit') }}</a>
              </td>
              <td width="12%">
                <a href="{{ url('admin/roles/delete/' ~ role.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i> {{ t('Delete') }}</a>
              </td>
            </tr>
          </tbody>
        {% if loop.last %}
          <tbody>
            <tr>
              <td colspan="10" align="right">
                <div class="btn-group">
                  <a href="{{ url('admin/roles/search') }}" class="btn btn-default"><i class="glyphicon glyphicon-fast-backward"></i> {{ t('First') }}</a>
                  <a href="{{ url('admin/roles/search?page=' ~ page.before) }}" class="btn btn-default"><i class="glyphicon glyphicon-step-backward"></i> {{ t('Previous') }}</a>
                  <a href="{{ url('admin/roles/search?page=' ~ page.next) }}" class="btn btn-default"><i class="glyphicon glyphicon-step-forward"></i> {{ t('Next') }}</a>
                  <a href="{{ url('admin/roles/search?page=' ~ page.last) }}" class="btn btn-default"><i class="glyphicon glyphicon-fast-forward"></i> {{ t('Last') }}</a>
                  <span class="help-inline">{{ page.current }}/{{ page.total_pages }}</span>
                </div>
              </td>
            </tr>
          <tbody>
        </table>
        {% endif %}
        {% else %}
          {{t('No roles are recorded')}}
        {% endfor %}
      </div>
    </div>
  </div>

</div>
