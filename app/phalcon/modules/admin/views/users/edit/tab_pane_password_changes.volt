<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{t('Id')}}</th>
        <th>{{t('IP Address')}}</th>
        <th>{{t('User Agent')}}</th>
        <th>{{t('Date')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for change in user.passwordChanges %}
      <tr>
        <td>{{ change.id }}</td>
        <td>{{ change.ipAddress }}</td>
        <td>{{ change.userAgent }}</td>
        <td>{{ date("Y-m-d H:i:s", change.createdAt) }}</td>
      </tr>
    {% else %}
      <tr><td colspan="4" align="center">{{t('User has not changed his/her password')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
