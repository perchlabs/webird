
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
    {% for namespace, resources in acl.getPrivateSpec() %}
      <div class="form-group">
        <div class="col-md-4 col-md-offset-4">
          <h2>{{ namespace }}</h2>
            {% for resource, actions in resources %}
              <table class="table table-bordered table-striped" align="center">
              <thead>
                <tr>
                  <th width="5%"></th>
                  <th>{{resource}}</th>
                </tr>
              </thead>
              <tbody>
                {% for action in actions %}
                <tr>
                  <td><input type="checkbox" name="permissions[]" value="{{ namespace ~ ':' ~ resource ~ '.' ~ action }}"  {% if permissions[namespace ~ ':' ~ resource ~ '.' ~ action] is defined %} checked="checked" {% endif %}></td>
                  <td>{{ action }}</td>
                </tr>
                {% endfor %}
              </tbody>
              </table>
            {% endfor %}
        </div>
      </div>
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
