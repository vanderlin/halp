


<!DOCTYPE html>
<html lang="en">
  <head>
    @include('site.layouts.head')
  </head>
  <body>

    <!-- Content -->
    <div class="container main-content">
      

    <div class="row">
    	<div class="col-md-6 col-md-offset-3">
	    	<div class="panel panel-default">
	    		<div class="panel-heading text-center"><h4>Please enter site password</h4></div>
	    		<div class="panel-body text-center">
					
					<div class="row">
						<form class="form col-md-6 col-md-offset-3" id="site-login-form" method="POST" action="{{{ URL::to('/site-login') }}}" accept-charset="UTF-8">
							<div class="form-group">
								<input type="password" class="form-control input-lg" id="site-password" name="site-password" placeholder="">
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-default">Enter</button>
							</div>
						</form>
					</div>
					<div class="row text-center">
						@include('site.partials.form-errors')
					</div>	
				</div>
			</div>
    	</div>
    </div>


    </div>
    <!-- ./ content -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
  
  </body>
</html>




