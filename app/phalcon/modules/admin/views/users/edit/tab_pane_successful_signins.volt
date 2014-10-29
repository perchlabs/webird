<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{_('Id')}}</th>
        <th>{{_('IP Address')}}</th>
        <th>{{_('User Agent')}}</th>
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
        <tr><td colspan="3" align="center">{{_('User does not have successfull signins')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
