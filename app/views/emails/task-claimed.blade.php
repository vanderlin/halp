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
				<img src="http://halp.ideo.com/assets/img/happy-turtle.png" />
				<h2>{{link_to($task->claimer->getProfileURL(), $task->claimer->getShortName())}} has claimed one of your tasks!</h2>
				<hr>
				<h3>You asked for help with:</h3>
				<h1>{{link_to($task->getURL(), $task->title.' for '.$task->project->title)}}</h1>
				<p>You estimated this task would take {{$task->duration}}. Go talk to {{link_to($task->claimer->getURL(), $task->claimer->firstname)}} and happy task-ing!</p>
			</div>
		</div>
	</body>
</html>