<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="pinterest" content="nopin" />

@if (isMobile())
  <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0' name='viewport' />
@else
  <meta name="viewport" content="width=device-width, initial-scale=1">
@endif


@include('site.layouts.favicons')
<title>
@section('title')
{{Config::get('config.site_name')}}
@show
</title>

<link href='http://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Montserrat:700' rel='stylesheet' type='text/css'>

<!-- Bootstrap
<link href="{{asset('assets/css/core/bootstrap.css')}}" rel="stylesheet"> -->
<link href="{{asset('assets/css/frontend/frontend.css')}}" rel="stylesheet">

<!-- core scripts -->
@include('site.layouts.core-scripts')
<script src="{{asset('assets/js/app.js')}}"></script>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

