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

    {% if config.services is defined %}
      {% if config.services.google is defined -%}
        <div class="form-group text-center">
          <div class="col-md-4 col-md-offset-4">
            <p>
              <a href="{{ url('signin/redirectoauth/google/') ~ security.getSessionToken() }}">Google</a>
            </p>
          </div>
        </div>
      {% endif %}
    {% endif %}

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

    <input type="hidden" name="{{ security.getTokenKey() }}" value="{{ security.getSessionToken() }}"/>
  </form>

  <div class="row">
    <div class="col-md-4 col-md-offset-4 text-center">
      <a href="{{ url('forgot-password') }}">{{ t('Forgot my password') }}</a>
    </div>
  </div>

</div>
