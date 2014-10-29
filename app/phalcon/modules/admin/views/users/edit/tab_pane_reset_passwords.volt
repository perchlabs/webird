<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{_('Id')}}</th>
        <th>{{_('Date')}}</th>
        <th>{{_('Reset?')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for reset in user.resetPasswords %}
      <tr>
        <th>{{ reset.id }}</th>
        <th>{{ date("Y-m-d H:i:s", reset.createdAt) }}
        <th>{{ reset.reset == 'Y' ? _('Yes') : _('No') }}
      </tr>
    {% else %}
      <tr><td colspan="3" align="center">{{_('User has not requested reset his/her password')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
