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
		<div style="text-align:center;">
			<div style="width:600px; padding: 100px; background-color:#fff; margin: 40px auto; border-bottom:6px solid #404d5b;">
				<img src="http://vanderlin.cc/deliver/ideo/temp/unhappy-turtle.png" />
				<h2 style="font-family:Georgia, serif; color:#404d5b; font-style:italic; font-size:26px; font-weight:100;">Alert! Alert! {{$task->creator->getShortName()}} needs help.</h2>
				<hr style="border: 0; width:10%; border-bottom:2px solid #404d5b; margin-top:40px; margin-bottom:40px;">
				<h3 style="color:#404d5b; text-transform:uppercase; font-family:Arial; font-weight:bold; font-size:13px;">{{$task->creator->firstname}} is looking for help with:</h3>
				<h1 style="font-family:Georgia, serif; color:#4FD2C2; font-size:45px; font-weight:100;">{{$task->title}} for {{$task->project->title}}</h1>
				<p style="font-family:Georgia, serif; color:#ccc; font-size:17px; font-weight:100; line-height:26px;">This task will take {{$task->duration}} to complete.<br>If you think you can help, claim the task on Halp.</p>
				<a href="{{URL::to($task->getURL())}}" style="color:#4FD2C2; text-decoration:none;"><div style="padding:20px; border:2px solid; border-radius:30px; text-transform:uppercase; font-family:Arial; font-size: 13px; font-weight:bold; width: 200px; margin:40px auto;">Go to Halp</div></a>
			</div>
		</div>
	</body>
</html>

