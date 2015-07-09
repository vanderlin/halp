@extends('site.layouts.default', ['use_navigation'=>false, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | {{$user->getName()}}
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')
  <div class="container" style="margin-top:40px">
    
    <div class="row">
      <div class="col-sm-8 col-sm-offset-2 text-center">
      
        <img src="{{$user->profileImage->url('s100')}}" class="img-circle">
        <h4>{{$user->getName()}}</h4>  
      	{{link_to('/', 'home')}}
      </div>
    </div>
@stop
    
