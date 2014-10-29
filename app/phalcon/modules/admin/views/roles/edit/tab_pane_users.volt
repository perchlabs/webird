<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{_('Id')}}</th>
        <th>{{_('Name')}}</th>
        <th>{{_('Banned?')}}</th>
        <th>{{_('Active?')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for user in role.users %}
      <tr>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.banned == 'Y' ? _('Yes') : _('No') }}</td>
        <td>{{ user.active == 'Y' ? _('Yes') : _('No') }}</td>
        <td width="12%">{{ link_to("users/edit/" ~ user.id, '<i class="glyphicon glyphicon-pencil"></i> ' ~ _('Edit'), "class": "btn btn-default") }}</td>
        <td width="12%">{{ link_to("users/delete/" ~ user.id, '<i class="glyphicon glyphicon-remove"></i> ' ~ _('Delete'), "class": "btn btn-default") }}</td>
      </tr>
    {% else %}
      <tr><td colspan="3" align="center">{{_('There are no users assigned to this role')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
