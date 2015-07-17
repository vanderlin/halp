<!DOCTYPE html>
<html lang="en">
  <head>
    @include('site.layouts.head')
    <link rel="stylesheet" type="text/css" href="{{bower('semantic-ui/dist/semantic.min.css')}}">
    <link href="{{asset('assets/css/backend/backend.css')}}" rel="stylesheet">
    @yield('head')
    @yield('scripts')
    <script src="{{bower('semantic-ui/dist/semantic.min.js')}}"></script>
  </head>
  <body>
   
    <!-- Content -->  
    <div class="container">
  
        <!-- Main Navigation -->
        @if (isset($use_navigation)?$use_navigation:true)
        @include('site.partials.main-navigation')
        @endif
        <!-- Main Navigation -->  
        
        <div class="admin">
            <div class="ui celled horizontal list">
                <div class="item">{{link_to('admin/users', 'Users')}}</div>
                <div class="item">{{link_to('admin/notifications', 'Notifications')}}</div>
                <div class="item">{{link_to('admin/projects', 'Projects')}}</div>
            </div>
            <br>
            <br>
            @yield('content')
        </div>   

    </div>
  
    <!-- Retina JS -->
    <script src="{{bower('retinajs/dist/retina.min.js') }}"></script>
    
    <script>
      (function() {
        // trim polyfill : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
        if (!String.prototype.trim) {
          (function() {
            // Make sure we trim BOM and NBSP
            var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
            String.prototype.trim = function() {
              return this.replace(rtrim, '');
            };
          })();
        }
        [].slice.call( document.querySelectorAll( 'input.input__field' ) ).forEach( function( inputEl ) {
          // in case the input is already filled..
          if( inputEl.value.trim() !== '' ) {
            classie.add( inputEl.parentNode, 'input--filled' );
          }
          // events:
          inputEl.addEventListener( 'focus', onInputFocus );
          inputEl.addEventListener( 'blur', onInputBlur );
        } );
        function onInputFocus( ev ) {
          classie.add( ev.target.parentNode, 'input--filled' );
        }
        function onInputBlur( ev ) {
          if( ev.target.value.trim() === '' ) {
            classie.remove( ev.target.parentNode, 'input--filled' );
          }
        }
      })();
    </script>

    @if (isset($use_footer)?$use_footer:true)
      @include('site.layouts.footer')
    @endif

    
  </body>
</html>




