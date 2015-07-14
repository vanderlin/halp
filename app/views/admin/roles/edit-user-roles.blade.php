@extends('admin.layouts.default', ['use_footer'=>false])

{{-- Web site Title --}}
@section('title')
  {{Config::get('config.site_name')}} | Admin | Edit Roles
@stop

@section('scripts')

@stop



{{-- Content --}}
@section('content')


<section class="admin">
    
    <div class="ui text container" style="text-align: left;">
        <div class="ui breadcrumb">
            <a href="{{URL::to('admin')}}" class="section">Admin</a>
                <div class="divider"> / </div>
            <a href="{{URL::to('admin/users')}}" class="section">Users</a>
            <div class="divider"> / </div>
            <div class="active section">{{$user->getName()}}</div>
        </div>
    </div>
    <br>

    <div class="ui text container" style="text-align: left">
        
        
        <img class="ui centered circular image" src="{{$user->profileImage->url('s100')}}">
        

        {{Form::open(['url'=>URL::to('admin/users/'.$user->id), 'method'=>'PUT', 'class'=>'ui form'])}}
        <div class="field">
            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Username" value="{{$user->username}}" disabled>
        </div>

        <div class="field">
          <label for="email">Email</label>
            <input type="email" id="email" placeholder="example@ideo.com" value="{{$user->email}}" disabled>
        </div>
     

        <div class="field">
            @include('admin.partials.roles', ['user'=>$user])
        </div>
        
        <button type="submit" class="ui button">Update</button> {{ link_to($user->getURL(), 'View Profile', ['class'=>'']) }}

      </div>

    {{Form::close()}}

    <div class="container text">
            @include('site.partials.form-errors')
    </div>

  </div>
</section>

@stop
