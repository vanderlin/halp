<!DOCTYPE html>
<html lang="en">
  <head>
    @include('site.layouts.head')
    @yield('head')
    @yield('scripts')
  </head>
  <body>
   
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




