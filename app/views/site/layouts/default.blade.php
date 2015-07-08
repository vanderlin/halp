<!DOCTYPE html>
<html lang="en">
  <head>
    @include('site.layouts.head')
    @yield('head')
    @yield('scripts')
  </head>
  <body>
    
    <!-- Main Navigation -->
    @if (isset($use_navigation)?$use_navigation:true)
      @include('site.layouts.main-navigation')
    @endif
    <!-- Main Navigation -->  


    <!-- Content -->
    @if (array_key_exists('content', View::getSections()))
      <div class="wrap">
      @yield('content')
      </div>
    @endif

    <!-- Retina JS -->
    <script src="{{ asset('bower_components/retinajs/dist/retina.min.js') }}"></script>

    @if (isset($use_footer)?$use_footer:true)
      @include('site.layouts.footer')
    @endif
    
  </body>
</html>




