<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Halp</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width"/>
	<link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Montserrat:700' rel='stylesheet' type='text/css'>
	<style type="text/css">
	{{File::get(public_path('assets/css/core/email.css'))}}
	</style>	

</head>
	<body>
		<table class="{{isset($css_class)?$css_class:''}}">
			<tr>
				<td>
					<div class="container">
						<div class="email-content">
							
							@if (array_key_exists('header', View::getSections()))
								@yield('header')
							@endif

							@yield('content')
								
							<br>
							
						</div>
					</div>
					<div class="footer">
						<a class="unsubscribe" href="{{URL::to('unsubscribe')}}">unsubscribe me</a>
						<div class="made-at-ideo">made at</div> 
        				<a href="http://ideo.com"><img src="{{production_url('assets/img/ideo-dark.png')}}" width="70px"></a>
					</div>
				</td>
			</tr>
		</table>
		@if (isset($extra))
			{{$extra}}
		@endif
	</body>
</html>

