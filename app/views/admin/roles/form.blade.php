<div class="form-group">
  
      <form method="POST" class="form-horizontal" role="form" action="{{{ URL::to('admin/roles') }}}" accept-charset="UTF-8">
        
        <div class="form-group">
          <label for="role-name" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="role-name" name="role-name" placeholder="Role name" value="">
          </div>
        </div>

        <div class="form-group row">
        <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default pull-right">Submit</button>
        </div>
        </div>


      </form>

      <div class="help-block text-center">
        @if (Session::get('roles-notice'))
          <div class="alert">{{{ Session::get('roles-notice') }}}</div>
        @endif
      </div>

</div>