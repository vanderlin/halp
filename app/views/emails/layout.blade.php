<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Halp</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width"/>
	<style type="text/css">
	{{File::get(public_path('assets/css/core/email.css'))}}
	</style>	
</head>
	<body>
		<table>
			<tr>
				<td>
					<div class="container">
						<div class="email-content">
							
							<img src="http://halp.ideo.com/assets/img/friends/{{get_random_task_image()}}" />

							@yield('content')
								
							<br>
							<a class="unsubscribe" href="{{URL::to('unsubscribe')}}">unsubscribe me</a>

						</div>
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>

