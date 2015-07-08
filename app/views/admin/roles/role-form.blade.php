<div class="panel panel-default">
  <div class="panel-heading">
    {{ $role->name }} <small>({{ $role->display_name }})</small>
    <a class="pull-right" href="{{URL::to('admin/roles/'.$role->id)}}"><span class="glyphicon glyphicon-pencil"></span></a>
  </div>
  
  <div class="panel-body">
    <form method="POST" class="form-horizontal" role="form" action="{{{ URL::to('admin/roles/'.$role->id) }}}" accept-charset="UTF-8">
      <input type="hidden" value="PUT" name="_method">
      @include('admin.partials.permissions', ['role'=>$role])
      <div class="form-group row">
        <div class="col-sm-offset-2 col-sm-10">
        
        <div class="pull-right">
          <button type="submit" class="btn btn-default">Update</button>
          <!-- <a class="btn btn-default btn-danger delete-role">Delete</a> -->
        </div>
        </div>
      </div>
    </form>
  </div>
</div>