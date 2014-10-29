{{ content() }}

<div class="container">
  <div class="row">
    <div class="col-md-1 text-left">
      {{ link_to("admin/users/index", "&larr; " ~ _('Go Back'), "class": "btn btn-link") }}
    </div>
    <div class="col-md-1 col-md-offset-10 text-right">
      {{ link_to("admin/users/create", _('Create user'), "class": "btn btn-primary") }}
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
            <th>{{_('Id')}}</th>
            <th>{{_('Name')}}</th>
            <th>{{_('Email')}}</th>
            <th>{{_('Role')}}</th>
            <th>{{_('Active')}}</th>
            <th>{{_('Banned')}}</th>
          </tr>
        </thead>
{% endif %}
      <tbody>
        <tr>
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.role.name }}</td>
          <td>
            {{ user.active == 'Y' ? _('Yes') : _('No') }}
            {% if user.active == 'N' and user.banned != 'Y' %}
              <br>HERE
            {% endif %}
          </td>
          <td>{{ user.banned == 'Y' ? _('Yes') : _('No') }}</td>
          <td colspan="2">
            {{ link_to("admin/users/edit/" ~ user.id, '<i class="glyphicon glyphicon-pencil"></i> ' ~ _('Edit'), "class": "btn btn-default") }}
            {{ link_to("admin/users/delete/" ~ user.id, '<i class="glyphicon glyphicon-remove"></i> ' ~ _('Delete'), "class": "btn btn-default") }}
          </td>
        </tr>
      </tbody>
{% if loop.last %}
      <tbody>
        <tr>
          <td colspan="10">
            <div class="btn-group pull-right">
              {{ link_to("admin/users/search", '<i class="glyphicon glyphicon-fast-backward"></i> ' ~ _('First'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.before, '<i class="glyphicon glyphicon-step-backward"></i> ' ~ _('Previous'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.next, '<i class="glyphicon glyphicon-step-forward"></i> ' ~ _('Next'), "class": "btn btn-default") }}
              {{ link_to("admin/users/search?page=" ~ page.last, '<i class="glyphicon glyphicon-fast-forward"></i> ' ~ _('Last'), "class": "btn btn-default") }}
              {{ page.current }}/{{ page.total_pages }}
            </div>
          </td>
        </tr>
      <tbody>
    </table>
{% endif %}
{% else %}
        {{_('No users are recorded')}}
{% endfor %}

      </div>
    </div>
  </div>
</div>
