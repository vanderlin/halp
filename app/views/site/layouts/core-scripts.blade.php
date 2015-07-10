
<script type="text/javascript">
	var User = (function() {
		var self = {};
		@if (Auth::check())
			self = {{Auth::User()->toJson()}};
		@endif 
		self.itineraries = null;
		return self;
	})();
</script>

<!-- fontawesome -->
<link rel="stylesheet" type="text/css" href="{{ bower('fontawesome/css/font-awesome.min.css') }}" />

<!-- jQuery + Extenstions -->
<script src="{{ bower('jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.extensions.js') }}"></script>

<!-- Jquery UI -->
<script src="{{ bower('jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Scroll To -->
<script type="text/javascript" src="{{ bower('jquery.scrollTo/jquery.scrollTo.min.js') }}"></script>

<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="{{bower('magnific-popup/dist/magnific-popup.css')}}"> 
<script src="{{bower('magnific-popup/dist/jquery.magnific-popup.js')}}"></script> 

<!-- time ago -->
<script src="{{bower('jquery-timeago/jquery.timeago.js')}}" type="text/javascript"></script>

<!-- select 2 -->
<link href="{{bower('select2/select2-bootstrap.css')}}" rel="stylesheet"/>   
<script src="{{bower('select2/select2.js')}}"></script>
