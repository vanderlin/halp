
@extends('admin.layouts.default')

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Edit Users
@stop

{{-- Content --}}
@section('content')



  <h1 class="page-header">
    {{ $user->username }}
  </h1>
  
  <div class="col-md-6">

    <form method="POST" class="form-horizontal" role="form" action="{{{ URL::to('admin/users/'.$user->id) }}}" accept-charset="UTF-8">
      <input type="hidden" value="PUT" name="_method">
      <div class="well row">
        <div class="form-group">
          <label for="username" class="col-sm-2 control-label">Username</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="username" placeholder="Username" value="{{$user->username}}" disabled>
          </div>
        </div>

        <div class="form-group">
          <label for="email" class="col-sm-2 control-label">Email</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="email" placeholder="example@ideo.com" value="{{$user->email}}" disabled>
          </div>
        </div>
      </div>

      <div class="well row">
        <div class="form-group">
          <label for="roles" class="col-sm-2 control-label">Roles</label>
          <div class="col-sm-10">
            @include('admin.partials.roles', ['user'=>$user])
          </div>
        </div>
      </div>



      <div class="form-group row">
        <div class="col-md-12 text-right">
          {{ link_to($user->getURL(), 'View Profile', ['class'=>'btn btn-default']) }}
          <button type="submit" class="btn btn-default">Update</button>

          {{--
          {{Form::open(['url'=>'admin/'.$user->id], 'method'=>'DELETE')}}

          {{Form::close()}}
          --}}

        </div>
      </div>

    </form>

    <div class="text-center">
        @if (Session::get('error'))
            <div class="alert alert-error">{{{ Session::get('error') }}}</div>
        @endif

        @if (Session::get('notice'))
            <div class="alert">{{{ Session::get('notice') }}}</div>
        @endif
    </div>

  </div>

@stop
