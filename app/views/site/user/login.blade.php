@extends('site.layouts.default', ['use_navigation'=>false, 'use_footer'=>false])

{{-- Web site Title --}}
@section('title')
{{Config::get('config.site_name')}} | Register
@stop

@section('head')
@stop

@section('scripts')
@stop


@section('content')
  
      <section class="introduction">
        <img src="{{asset('assets/content/img/halp-home.png')}}" width="150px" height="68px"/>
        <div class="logo-text">halp.</div>
        <div class="introduction-content">
          <span class="intro-text">This is a tool for finding help with a task that will take <strong>less than one day</strong> to complete. Login with your <strong>Gmail</strong> username and password.</span>
        </div>
        <div class="box login-button">
          <!-- progress button -->
          <div class="progress-button elastic">
            <a href="{{GoogleSessionController::generateGoogleLoginURL()}}"><button><span>SIGN IN WITH GOOGLE</span></button></a>
            <svg class="progress-circle" width="70" height="70"><path d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z"/></svg>
            <svg class="checkmark" width="70" height="70"><path d="m31.5,46.5l15.3,-23.2"/><path d="m31.5,46.5l-8.5,-7.1"/></svg>
            <svg class="cross" width="70" height="70"><path d="m35,35l-9.3,-9.3"/><path d="m35,35l9.3,9.3"/><path d="m35,35l-9.3,9.3"/><path d="m35,35l9.3,-9.3"/></svg>
          </div>
          <!-- /progress-button -->
          <!-- progress button - FAILURE
          <div class="progress-button elastic">
            <button><span>Submit</span></button>
            <svg class="progress-circle" width="70" height="70"><path d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z"/></svg>
            <svg class="checkmark" width="70" height="70"><path d="m31.5,46.5l15.3,-23.2"/><path d="m31.5,46.5l-8.5,-7.1"/></svg>
            <svg class="cross" width="70" height="70"><path d="m35,35l-9.3,-9.3"/><path d="m35,35l9.3,9.3"/><path d="m35,35l-9.3,9.3"/><path d="m35,35l9.3,-9.3"/></svg>
          </div> -->
        </div>
      </section>
      

  {{--
  <div class="container" style="margin-top:40px">
    <div class="row">
      <div class="col-sm-6 col-md-4 col-md-offset-4">
        <div class="panel panel-default">
          
          <div class="panel-heading">
            <strong>Sign in to continue</strong>
          </div>

          <div class="panel-body">
            <form role="form" action="#" method="POST">
              
              <fieldset>
                
                <div class="row">
                  <div class="text-center">
                    <h1><i class="fa fa-user"></i></h1>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                    
                    @if (isset($use_fields))
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-user"></i>
                          </span> 
                          <input class="form-control" placeholder="Username" name="loginname" type="text" autofocus>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="glyphicon glyphicon-lock"></i>
                          </span>
                          <input class="form-control" placeholder="Password" name="password" type="password" value="">
                        </div>
                      </div>

                      <div class="form-group">
                        <input type="submit" class="btn btn-lg btn-primary btn-block" value="Sign in">
                      </div>

                    @else 
                    <div class="form-group">
                      <a href="{{GoogleSessionController::generateGoogleLoginURL()}}" class="btn btn-lg btn-primary btn-block">Sign in with Google+</a>
                    </div>
                    @endif

                  </div>
                </div>
              </fieldset>
            </form>
          </div>
          <div class="panel-footer "></div>
          </div>
      </div>
    </div>
  </div>
  --}}
@stop
    
