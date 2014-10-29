
<div class="container">

  <div class="row text-left">
    <div class="col-md-4 col-md-offset-4">
      <h2>Sign Up</h2>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4 col-md-offset-4">
      {{ content() }}
    </div>
  </div>

  {{ form('class': 'form-horizontal', 'role': 'form') }}

    <div class="form-group top17">
      <label for="name" class="col-md-2 col-md-offset-2 control-label">{{ form.label('name') }}</label>
      <div class="col-md-4">
        {{ form.render("name", ['class':'form-control']) }}
        {% if form.hasMessagesFor('name') %}
          <div class="text-danger">{{ form.getMessagesFor('name')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="email" class="col-md-2 col-md-offset-2 control-label">{{ form.label('email') }}</label>
      <div class="col-md-4">
        {{ form.render("email", ['class':'form-control']) }}
        {% if form.hasMessagesFor('email') %}
          <div class="text-danger">{{ form.getMessagesFor('email')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="password" class="col-md-2 col-md-offset-2 control-label">{{ form.label('password') }}</label>
      <div class="col-md-4">
        {{ form.render("password", ['class':'form-control']) }}
        {% if form.hasMessagesFor('password') %}
          <div class="text-danger">{{ form.getMessagesFor('password')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="confirmPassword" class="col-md-2 col-md-offset-2 control-label">{{ form.label('confirmPassword') }}</label>
      <div class="col-md-4">
        {{ form.render("confirmPassword", ['class':'form-control']) }}
        {% if form.hasMessagesFor('confirmPassword') %}
          <div class="text-danger">{{ form.getMessagesFor('confirmPassword')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <label for="terms" class="col-md-2 col-md-offset-2 control-label">{{ form.label('terms') }}</label>
      <div class="col-md-4">
        {{ form.render("terms", ['class':'form-control']) }}
        {% if form.hasMessagesFor('terms') %}
          <div class="text-danger">{{ form.getMessagesFor('terms')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ form.render("Sign Up") }}
      </div>
    </div>


    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ form.render('csrf', ['value': security.getToken()]) }}
        {% if form.hasMessagesFor('csrf') %}
          <div class="text-danger">{{ form.getMessagesFor('csrf')[0] }}</div>
        {% endif %}
      </div>
    </div>

  </form>

</div>
