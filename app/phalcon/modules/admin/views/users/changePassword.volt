<div class="container">
  {{ form('users/changePassword', 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        <h2>{{t('Change Password')}}</h2>
        <div class="text-warning">
          {{ content() }}
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="name" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('password')}}</label>
      <div class="col-md-4">
        {{ form.render('password', ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <label for="email" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('confirmPassword')}}</label>
      <div class="col-md-4">
        {{ form.render("confirmPassword", ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ submit_button(t('Change Password'), "class": "btn btn-primary") }}
      </div>
    </div>

  </form>
</div>
