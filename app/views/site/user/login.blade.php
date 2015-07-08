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
@stop
    
