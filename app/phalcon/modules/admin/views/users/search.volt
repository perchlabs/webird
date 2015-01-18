{{ content() }}

<div class="container">
  <div class="row">
    <div class="col-md-1 text-left">
      {{ link_to("admin/users/index", "&larr; " ~ t('Go Back'), "class": "btn btn-link") }}
    </div>
    <div class="col-md-1 col-md-offset-10 text-right">
      {{ link_to("admin/users/create", t('Create user'), "class": "btn btn-primary") }}
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
            {{ link_to("admin/users/edit/" ~ user.id, '<i class="glyphicon glyphicon-pencil"></i> ' ~ t('Edit'), "class": "btn btn-default") }}
            {% if user.isDeleted() %}
              <span class="btn btn-default disabled" role="button">{{t('Delete')}}</span>
            {% else %}
              {{ link_to("admin/users/delete/" ~ user.id, '<i class="glyphicon glyphicon-remove"></i> ' ~ t('Delete'), "class": "btn btn-default") }}
            {% endif %}
          </td>
        </tr>
      </tbody>
{% if loop.last %}
      <tbody>
        <tr>
          <td colspan="10">
            <div class="btn-group pull-right">
              {{ link_to("admin/users/search", '<i class="glyphicon glyphicon-fast-backward"></i> ' ~ t('First'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.before, '<i class="glyphicon glyphicon-step-backward"></i> ' ~ t('Previous'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.next, '<i class="glyphicon glyphicon-step-forward"></i> ' ~ t('Next'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.last, '<i class="glyphicon glyphicon-fast-forward"></i> ' ~ t('Last'), "class": "btn btn-default") }}
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
