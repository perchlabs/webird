<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{t('Id')}}</th>
        <th>{{t('Name')}}</th>
        <th>{{t('Banned?')}}</th>
        <th>{{t('Active?')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for user in role.users %}
      <tr>
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.banned == 'Y' ? t('Yes') : t('No') }}</td>
        <td>{{ user.active == 'Y' ? t('Yes') : t('No') }}</td>
        <td width="12%">
          <a href="{{ url('admin/users/edit/' ~ user.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-pencil"></i> {{ t('Edit') }}</a>
        </td>
        <td width="12%">
          <a href="{{ url('admin/users/delete/' ~ user.id) }}" class="btn btn-default"><i class="glyphicon glyphicon-remove"></i> {{ t('Delete') }}</a>
        </td>
      </tr>
    {% else %}
      <tr><td colspan="3" align="center">{{t('There are no users assigned to this role')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
