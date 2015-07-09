<!DOCTYPE html>
<html lang="en">
  <head>
    @include('site.layouts.head')
    @yield('head')
    @yield('scripts')
  </head>
  <body class="dark-bg">
   
    <!-- Content -->  
    <div class="container">
  
      <!-- Main Navigation -->
      @if (isset($use_navigation)?$use_navigation:true)
        @include('site.partials.main-navigation')
      @endif
      <!-- Main Navigation -->  


      @yield('content')


    </div>
  
    <!-- Retina JS -->
    <script src="{{ asset('bower_components/retinajs/dist/retina.min.js') }}"></script>
    <script src="{{asset('assets/js/classie.js')}}"></script>
    <script src="{{asset('assets/js/uiProgressButton.js')}}"></script>


    @if (isset($use_footer)?$use_footer:true)
      @include('site.layouts.footer')
    @endif

    
  </body>
</html>




