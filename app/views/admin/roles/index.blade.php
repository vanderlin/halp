
    
    



@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  LocalsOnly | Roles &amp; Permissions
@stop

{{-- Content --}}
@section('content')
  
  <!-- <div class="row">
    
    <div class="col-md-6">
      
    -->
    
    <div class="page-header">
      <h2>Roles &amp; Permissions</h2>
    </div>
      
    <div class="row">
      <div class="col-md-12">
        <h3>Roles</h3>
        <div class="row">
          @foreach (Role::all() as $role)
            <div class="col-sm-4">
              @include('admin.roles.role-form', array('role' => $role))
            </div>
          @endforeach
        </div>
      </div>
    </div>    
  


    <div class="row">
      <div class="col-md-12">
        <h3>Permissions</h3>
        <div class="row">
          <div class="col-sm-6">
            <div class="list-group">
              @foreach (Permission::all() as $permission)
                <div class="list-group-item">
                  {{$permission->name}}
                </div>
              @endforeach
            </div>
          </div>   
        </div>
      </div>
    </div>    
      




      <div class="row">
        <div class="col-md-6">
          <h3>Add New Role</h3>
          <div class="well">
          @include('admin.roles.form')
          </div>
        </div>
      </div>


      <div class="row">
        <div class="col-md-6">
          <h3>Add New Permission</h3>
          <div class="well">
          @include('admin.permissions.form')
          </div>
        </div>
      </div>
    <!-- </div>
</div> -->

@stop

