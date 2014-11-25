
<!-- security token: {{security.getToken()}} -->
<div class="container">
  {{ form('class': 'form-horizontal', 'role': 'form') }}

    <div class="form-group text-left">
      <div class="col-md-4 col-md-offset-4">
        <h2>Sign In</h2>
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        {{ content() }}
      </div>
    </div>

    <div class="form-group text-center">
      <div class="col-md-4 col-md-offset-4">
        <p>
          with:
          {{ link_to('signin/redirectoauth/google/'~security.getSessionToken(), "Google") }}
      <!--      {{ link_to('signin/redirectoauth/microsoft/'~security.getSessionToken(), "Microsoft") }}-->
        </p>
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        {{ form.render('email', ['class':'form-control']) }}
        {% if form.hasMessagesFor('email') %}
          <div class="text-danger">{{ form.getMessagesFor('email')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        {{ form.render('password', ['class':'form-control']) }}
        {% if form.hasMessagesFor('password') %}
          <div class="text-danger">{{ form.getMessagesFor('password')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ form.render('submit', ['class':'btn btn-success']) }}
      </div>
    </div>

    <div class="form-group remember">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ form.label('remember') }}
        {{ form.render('remember') }}
      </div>
    </div>

    {{ form.render('csrf', ['value': security.getSessionToken()]) }}

  </form>

  <div class="row">
    <div class="col-md-4 col-md-offset-4 text-center">
      {{ link_to("forgot-password", "Forgot my password") }}
    </div>
  </div>

</div>
