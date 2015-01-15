<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{t('Id')}}</th>
        <th>{{t('IP Address')}}</th>
        <th>{{t('User Agent')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for signin in user.successSignins %}
      <tr>
        <td>{{ signin.id }}</td>
        <td>{{ signin.ipAddress }}</td>
        <td>{{ signin.userAgent }}</td>
      </tr>
    {% else %}
        <tr><td colspan="3" align="center">{{t('User does not have successfull signins')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
