{{ content() }}

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h3>{{t("Admin Utilities")}}</h3>
    </div>
  </div>

  <div class="row top7">
    <div class="col-md-4">
    <ul class="list-group">
    {%- set utilities = [
      'admin/users': t('User Management'),
      'admin/roles': t('Role Management'),
      'admin/permissions': t('Permissions Management')
    ] -%}
    {%- for key, value in utilities %}
      <li class="list-group-item">{{ link_to(key, value) }}</li>
    {%- endfor -%}
    </ul>
    </div>
  </div>
</div>
