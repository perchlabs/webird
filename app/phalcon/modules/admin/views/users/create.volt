<div class="container">
  <div class="row">
    <div class="col-md-1">
      {{ link_to("admin/users", '&larr; ' ~ _('Go Back'), "class": "btn btn-link pull-left") }}
    </div>
  </div>

  {{ form(null, 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}
    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        <h2>{{_('Create a User')}}</h2>
        <div class="text-warning">
          {{ content() }}
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="name" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('name')}}</label>
      <div class="col-md-4">
        {{ form.render("name", ['class':'form-control']) }}
        {% if form.hasMessagesFor('name') %}
          <div class="text-danger">{{ form.getMessagesFor('name')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="email" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('email')}}</label>
      <div class="col-md-4">
        {{ form.render("email", ['class':'form-control']) }}
        {% if form.hasMessagesFor('email') %}
          <div class="text-danger">{{ form.getMessagesFor('email')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="rolesId" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('rolesId')}}</label>
      <div class="col-md-4">
        {{ form.render("rolesId", ['class':'form-control']) }}
        {% if form.hasMessagesFor('rolesId') %}
          <div class="text-danger">{{ form.getMessagesFor('rolesId')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="active" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('emailActivationMsg')}}</label>
      <div class="col-md-4">
        {{ form.render("emailActivationMsg", ['class':'form-control']) }}
        <p class="text-warning">{{_('* The email can be sent at a later time')}}</p>
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ form.render('submit', ['class':'btn btn-success']) }}
      </div>
    </div>
  </form>
</div>
