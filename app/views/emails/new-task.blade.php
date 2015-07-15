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
		<div class="container">
			<div class="email-content">
				<img src="http://vanderlin.cc/deliver/ideo/temp/unhappy-turtle.png" />
				<h2>Alert! Alert! {{$task->creator->getShortName()}} needs help.</h2>
				<hr>
				<h3>{{$task->creator->firstname}} is looking for help with:</h3>
				<h1>{{$task->title}} for {{$task->project->title}}</h1>
				<p>This task will take {{$task->duration}} to complete. If you think you can help, claim the task on Halp.</p>
				<a href="{{URL::to($task->getURL())}}"><div class="rounded-button">Go to Halp</div></a>
			</div>
		</div>
	</body>
</html>

