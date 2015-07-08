<!DOCTYPE html>
<html lang="en">
  
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('site.layouts.favicons')

    <title>
      @section('title')
      {{Config::get('config.site_name')}} | Admin
      @show
    </title>

    @include('site.layouts.core-scripts')

    <!-- wysiwyg -->
    <script src="{{bower('bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.all.min.js')}}"></script>
    <link href="{{bower('bootstrap3-wysihtml5-bower/dist/bootstrap3-wysihtml5.min.css')}}" rel="stylesheet"/>

    <!-- Bootstrap -->
    <link href="{{asset('assets/css/core/bootstrap.css')}}" rel="stylesheet">
    <link href="{{bower('select2/select2.css')}}" rel="stylesheet"/>  
    <link href="{{asset('assets/css/backend/backend.css')}}" rel="stylesheet">    
  
    <!-- modules -->
    <script src="{{ asset('assets/js/modules/deleteAsset.js') }}"></script>
    

  
  <script src="{{bower('moment/min/moment.min.js')}}"></script>
  <script src="{{bower('eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
  <link rel="stylesheet" href="{{bower('eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}"/>

    <!-- <script src="{{asset('assets/js/app.js')}}"></script> -->   
    

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->



    @yield('scripts')

  </head>

  <body>
    <div id="wrapper">

      {{-- Navigation --}}
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
          @include('admin.layouts.top-navigation');
      </nav>



      {{-- Admin Content --}}
      <div id="page-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
      </div>
      {{-- Admin Content --}}

      
    </div>

    <div class="side-bar-wrapper">
      @include('admin.layouts.sidebar')
    </div>
    
    <!-- Retina JS -->
    <script src="{{ asset('bower_components/retinajs/dist/retina.min.js') }}"></script>

  </body>
</html>




