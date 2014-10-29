
<div class="container">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <h2>{{_('Role Permissions')}}</h2>
      <div class="text-warning">
        {{ content() }}
      </div>
    </div>
  </div>

  {{ form(null, 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}

    <div class="form-group">
      <div class="col-md-6 col-md-offset-3">
        {{ select('roleId', roles, 'class':'form-control', 'using': ['id', 'name'], 'useEmpty': true, 'emptyText': '...', 'emptyValue': '') }}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-6 col-md-offset-3">
        {{ submit_button(_('Search'), 'name': 'search', 'class': 'btn btn-primary') }}
      </div>
    </div>

    {% if request.isPost() and role %}
    {% for resource, actions in acl.getPrivateResources() %}
    {# Show only resources that have at least one private action #}
    {% if actions|length > 0 %}
    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        <h3>{{ resource }}</h3>
        <table class="table table-bordered table-striped" align="center">
          <thead>
            <tr>
              <th width="5%"></th>
              <th>{{_('Description')}}</th>
            </tr>
          </thead>
          <tbody>
            {% for action in actions %}
            <tr>
              <td align="center"><input type="checkbox" name="permissions[]" value="{{ resource ~ '.' ~ action }}"  {% if permissions[resource ~ '.' ~ action] is defined %} checked="checked" {% endif %}></td>
              <td>{{ action }}</td>
            </tr>
            {% endfor %}
          </tbody>
        </table>
      </div>
    </div>
    {% endif %}
    {% endfor %}
    {% endif %}

    {% if request.isPost() and role %}
    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ submit_button(_('Save'), "name": "save", "class": "btn btn-success") }}
      </div>
    </div>
    {% endif %}

  </form>
</div>
