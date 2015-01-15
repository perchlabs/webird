<p>
  <table class="table table-bordered table-striped" align="center">
    <thead>
      <tr>
        <th>{{t('Id')}}</th>
        <th>{{t('Date')}}</th>
        <th>{{t('Reset?')}}</th>
      </tr>
    </thead>
    <tbody>
    {% for reset in user.resetPasswords %}
      <tr>
        <th>{{ reset.id }}</th>
        <th>{{ date("Y-m-d H:i:s", reset.createdAt) }}
        <th>{{ reset.reset == 'Y' ? t('Yes') : t('No') }}
      </tr>
    {% else %}
      <tr><td colspan="3" align="center">{{t('User has not requested reset his/her password')}}</td></tr>
    {% endfor %}
    </tbody>
  </table>
</p>
