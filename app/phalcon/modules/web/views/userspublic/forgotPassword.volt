<div class="container">

  {{ form('class': 'form-horizontal', 'role': 'form') }}

    <div class="form-group text-left">
      <div class="col-md-4 col-md-offset-4">
        <h2>{{t('Forgot Password?')}}</h2>
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        {{ content() }}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        {{ form.render('email', ['action':'/session/forgot-password', 'class':'form-control', 'value':'']) }}
        {% if form.hasMessagesFor('email') %}
          <div class="text-danger">{{ form.getMessagesFor('email')[0] }}</div>
        {% endif %}
      </div>
    </div>

    <div class="form-group text-left">
      <div class="col-md-4 col-md-offset-4 text-center">
          {{ form.render('send') }}
      </div>
    </div>
  </form>
</div>
