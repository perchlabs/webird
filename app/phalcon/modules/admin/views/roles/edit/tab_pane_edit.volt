
{{ form(null, 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}
  {{ form.render("id") }}

  <div class="form-group">
    <label for="name" class="col-md-2 col-md-offset-2  control-label">{{ form.label('name') }}</label>
    <div class="col-md-4">
      {{ form.render("name", ['class':'form-control']) }}
      {% if form.hasMessagesFor('name') %}
        <div class="text-danger">{{ form.getMessagesFor('name')[0] }}</div>
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
    <div class="col-sm-12 text-center">
      {{ form.render('submit', ['class':'btn btn-success']) }}
    </div>
  </div>
</form>
