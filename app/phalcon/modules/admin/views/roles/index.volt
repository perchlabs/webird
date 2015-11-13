
<div class="container">
  <div class="row">
    <div class="col-sm-1 pull-right">
      <a href="{{ url('admin/roles/create') }}" class="btn btn-primary text-right"><i class="glyphicon glyphicon-plus-sign"></i> {{t('Create Roles')}}</a>
    </div>
  </div>

  {{ form('admin/roles/search', 'class': 'form-horizontal', 'autocomplete': 'off', 'role': 'form') }}
    <div class="form-group">
      <div class="col-md-4 col-md-offset-4">
        <h2>{{t('Search roles')}}</h2>
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
      <div class="col-md-4 col-md-offset-4 text-center">
        {{ submit_button(t('Search'), "class": "btn btn-primary") }}
      </div>
    </div>

  </form>

</div>
