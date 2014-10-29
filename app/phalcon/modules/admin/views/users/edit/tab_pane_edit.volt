
{{ form(null, 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}
  {{ form.render("id", ['class':'form-control']) }}

  <div class="form-group">
    <label for="name" class="col-md-2 col-md-offset-2 control-label">{{ form.label('name') }}</label>
    <div class="col-md-4">
      {{ form.render("name", ['class':'form-control']) }}
      {% if form.hasMessagesFor('name') %}
        <div class="text-danger">{{ form.getMessagesFor('name')[0] }}</div>
      {% endif %}
    </div>
  </div>

  <div class="form-group">
    <label for="email" class="col-md-2 col-md-offset-2  control-label">{{ form.label('email') }}</label>
    <div class="col-md-4">
      {{ form.render("email", ['class':'form-control']) }}
      {% if form.hasMessagesFor('email') %}
        <div class="text-danger">{{ form.getMessagesFor('email')[0] }}</div>
      {% endif %}
    </div>
  </div>

  <div class="form-group">
    <label for="rolesId" class="col-md-2 col-md-offset-2  control-label">{{ form.label('rolesId') }}</label>
    <div class="col-md-4">
      {{ form.render("rolesId", ['class':'form-control']) }}
      {% if form.hasMessagesFor('rolesId') %}
        <div class="text-danger">{{ form.getMessagesFor('rolesId')[0] }}</div>
      {% endif %}
    </div>
  </div>

  <div class="form-group">
    <label for="active" class="col-md-2 col-md-offset-2  control-label">{{ form.label('active') }}</label>
    <div class="col-md-4">
      {{ form.render("active", ['class':'form-control']) }}
      {% if form.hasMessagesFor('active') %}
        <div class="text-danger">{{ form.getMessagesFor('active')[0] }}</div>
      {% endif %}
    </div>
  </div>

  <div class="form-group">
    <label for="banned" class="col-md-2 col-md-offset-2  control-label">{{ form.label('banned') }}</label>
    <div class="col-md-4">
      {{ form.render("banned", ['class':'form-control']) }}
      {% if form.hasMessagesFor('banned') %}
        <div class="text-danger">{{ form.getMessagesFor('banned')[0] }}</div>
      {% endif %}
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-4 col-md-offset-4 text-center">
      {{ form.render('submit', ['class':'btn btn-success']) }}
    </div>
  </div>

</form>
