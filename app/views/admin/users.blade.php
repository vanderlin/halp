@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  Admin | Section 
@stop


@section('scripts')
<script type="text/javascript">

$(document).ready(function($) {
  
  $("#user-filter").change(function(event) {
    var $form = $(this);

    var office = $form.find('input[name="office[]"]:checked');
    var officeIDs = [];
    for (var i = 0; i < office.length; i++) {
      officeIDs.push($(office[i]).val());
    };


    var roles = $form.find('input[name="roles[]"]:checked');
    var roleIDs = [];
    for (var i = 0; i < roles.length; i++) {
      roleIDs.push($(roles[i]).val());
    };

    var url = "/admin/users?office="+officeIDs.join(",")+"&roles="+roleIDs.join(",");
    document.location = url;
    

  });

});


</script>
@stop

{{-- Content --}}
@section('content')
  
  <h2 class="page-header">Users</h2>


  <div class="panel">
  {{Form::open(['url'=>'/api/users', 'method'=>'POST', 'id'=>'user-filter'])}}  
  <div class="row">
    <div class="col-sm-2">
      <h5>Office</h5>
      <?php $ids = explode(",", Input::get('office')); ?>
      @foreach (Office::all() as $office)
      <div class="checkbox">
        <label>
          <input type="checkbox" value="{{$office->id}}" name="office[]" {{in_array($office->id, $ids)?'checked':''}}>
          {{ $office->name }}
        </label>
      </div>
      @endforeach
    </div>

    <div class="col-sm-2">
      <h5>Roles</h5>
        <?php $ids = explode(",", Input::get('roles')); ?>
       @foreach (Role::all() as $role)
      <div class="checkbox">
        <label>
          <input type="checkbox" value="{{$role->id}}" name="roles[]" {{in_array($role->id, $ids)?'checked':''}}>
          {{ $role->display_name }}
        </label>
      </div>
      @endforeach
    </div>

  </div>    
  {{Form::close()}}
  </div>


  <div class="table-responsive">
    <table class="table table-striped">
    
      <thead>
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th></th>
        </tr>
      </thead>

      <tbody>
        @foreach ($users as $user)
          
          <tr>
            <td>{{ $user->id }}</td>
            <td>
              <ul class="list-unstyled">
              <li>
                <img width="40" height="40" src="{{$user->profileImage->url('s40')}}" class="pull-left img-circle">
                <ul class="list-unstyled">
                  <li>{{ link_to($user->getProfileURL(), $user->username) }}</li>
                  <li><small>{{$user->getRoles()}}</small></li>
                </ul>
                
                </li>
              
              </ul>
            </td>
            <td>{{ link_to('mailto:'.$user->email, $user->email)}}</td>
            <td>{{ link_to('admin/users/'.$user->id, 'Edit', ['class'=>'btn btn-default btn-sm'])}}</td>
          </tr>

        @endforeach
      </tbody>
    </table>
  </div>

@stop
