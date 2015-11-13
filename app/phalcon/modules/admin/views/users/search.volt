{{ content() }}

<div class="container">
  <div class="row">
    <div class="col-md-1 text-left">
      <a href="{{ url('admin/users/index') }}" class="btn btn-link">&larr; {{ t('Go Back') }}</a>
    </div>
    <div class="col-md-1 col-md-offset-10 text-right">
      <a href="{{ url('admin/users/create') }}" class="btn btn-primary">{{ t('Create User') }}</a>
    </div>
  </div>

  <div class="row top10">
    <div class="col-md-12">
      <div class="table-responsive">
{% for user in page.items %}
{% if loop.first %}
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>{{t('Id')}}</th>
            <th>{{t('Name')}}</th>
            <th>{{t('Email')}}</th>
            <th>{{t('Role')}}</th>
            <th>{{t('Active')}}</th>
            <th>{{t('Banned')}}</th>
          </tr>
        </thead>
{% endif %}
      <tbody>
        {% if user.isDeleted() %}<tr class="danger">{% else %}<tr>{% endif %}
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.role.name }}</td>
          <td>{{ user.isActive() ? t('Yes') : t('No') }}</td>
          <td>{{ user.isBanned() ? t('Yes') : t('No') }}</td>
          <td colspan="2">
            <a href="{{ url('admin/users/edit/' ~ user.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-pencil"></i> {{ t('Edit') }}</a>
            {% if user.isDeleted() %}
              <span class="btn btn-default disabled" role="button">{{t('Delete')}}</span>
            {% else %}
              <a href="{{ url('admin/users/delete/' ~ user.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i> {{ t('Delete') }}</a>
            {% endif %}
          </td>
        </tr>
      </tbody>
{% if loop.last %}
      <tbody>
        <tr>
          <td colspan="10">
            <div class="btn-group pull-right">
              <a href="{{ url('admin/users/search') }}" class="btn btn-default"><i class="glyphicon glyphicon-fast-backward"></i> {{ t('First')}}</a>
              <a href="{{ url('admin/users/search?page=' ~ page.before) }}" class="btn btn-default"><i class="glyphicon glyphicon-step-backward"></i> {{ t('Previous')}}</a>
              <a href="{{ url('admin/users/search?page=' ~ page.next) }}" class="btn btn-default"><i class="glyphicon glyphicon-step-forward"></i> {{ t('Next')}}</a>
              <a href="{{ url('admin/users/search?page=' ~ page.last) }}" class="btn btn-default"><i class="glyphicon glyphicon-fast-forward"></i> {{ t('Last')}}</a>
              {{ page.current }}/{{ page.total_pages }}
            </div>
          </td>
        </tr>
      <tbody>
    </table>
{% endif %}
{% else %}
        {{t('No users are recorded')}}
{% endfor %}

      </div>
    </div>
  </div>
</div>
