<div class="container">
  <div class="row pull-right">
    <div class="col-sm-1 text-right">
      <a href="{{ url('admin/users/create') }}" class="btn btn-primary pull-right"><i class='glyphicon glyphicon-plus-sign'></i> {{ t('Create User') }}</a>
    </div>
  </div>

  {{ form('admin/users/search', 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}
    <div class="form-group text-left">
      <div class="col-md-4 col-md-offset-4">
        <h2>{{t('Search users')}}</h2>
        <div class="text-warning">
          {{ content() }}
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="id" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('id')}}</label>
      <div class="col-md-4">
        {{ form.render("id", ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <label for="name" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('name')}}</label>
      <div class="col-md-4">
        {{ form.render("name", ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <label for="email" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('email')}}</label>
      <div class="col-md-4">
        {{ form.render("email", ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <label for="rolesId" class="col-md-2 col-md-offset-2 text-right control-label">{{form.label('rolesId')}}</label>
      <div class="col-md-4">
        {{ form.render("rolesId", ['class':'form-control']) }}
      </div>
    </div>

    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        <p class="text-center">{{ submit_button(t('Search'), "class": "btn btn-primary") }}</p>
      </div>
    </div>

  </form>

</div>
