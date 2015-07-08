<div class="form-group">

      <form method="POST" class="form-horizontal" role="form" action="{{{ URL::to('admin/permissions') }}}" accept-charset="UTF-8">
        
       
        <div class="form-group">
          <label for="permision-name" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="name" id="permision-name" placeholder="ie: edit_posts">
          </div>
        </div>

        <div class="form-group">
          <label for="permision-display-name" class="col-sm-2 control-label">Display Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="display_name" id="permision-display-name" placeholder="ie: Edit Posts">
          </div>
        </div>

        <div class="form-group row">
        <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default pull-right">Submit</button>
        </div>
        </div>


      </form>

      <div class="help-block text-center">
        @if (Session::get('permissions-notice'))
          <div class="alert">{{{ Session::get('permissions-notice') }}}</div>
        @endif
        @if (Session::get('permissions-errors'))
          <div class="alert-danger">
          	<ul class="list-unstyled">
	          	@foreach (Session::get('permissions-errors')->all() as $error)
	          		<li>{{ $error }}</li>
	          	@endforeach
          	</ul>
          </div>
        @endif
        
      </div>

</div>