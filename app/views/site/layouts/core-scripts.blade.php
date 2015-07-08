
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


<!-- fonts -->
@if (App::environment() == 'local')
<link href="{{ asset('assets/fonts/fonts.css') }}" rel="stylesheet">
@else 
<link rel="stylesheet" type="text/css" href="//cloud.typography.com/6915872/755066/css/fonts.css" />
<!-- <link rel="stylesheet" type="text/css" href="//cloud.typography.com/6915872/755066/css/fonts.css" /> -->
@endif

<!-- fontawesome -->
<link rel="stylesheet" type="text/css" href="{{ bower('fontawesome/css/font-awesome.min.css') }}" />

<!-- Google maps API -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
<script src="{{asset('assets/js/google-map-style.js')}}"></script>
<script src="{{asset('assets/js/markerclusterer.js')}}"></script>
<script src="{{asset('assets/js/markerwithlabel.js')}}"></script>


<!-- jQuery + Extenstions -->
<script src="{{ bower('jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.extensions.js') }}"></script>

<!-- Swiper JS -->
<link rel="stylesheet" href="{{bower('swiper/dist/css/swiper.min.css')}}">
<script src="{{bower('swiper/dist/js/swiper.min.js')}}"></script>

{{-- autosize --}}
<script src="{{ bower('autosize/dest/autosize.min.js') }}"></script>

<!-- Jquery UI -->
<!-- <link href="{{ bower('jquery-ui/jquery-ui.css') }}" rel="stylesheet"> -->
<script src="{{ bower('jquery-ui/jquery-ui.min.js') }}"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="{{ bower('bootstrap/dist/js/bootstrap.min.js') }}"></script>


<script type="text/javascript" src="{{ bower('masonry/dist/masonry.pkgd.min.js') }}"></script>
<script type="text/javascript" src="{{ bower('imagesloaded/imagesloaded.pkgd.min.js') }}"></script>

<!-- Scroll To -->
<script type="text/javascript" src="{{ bower('jquery.scrollTo/jquery.scrollTo.min.js') }}"></script>

<!-- scrollbars -->
<link href="{{ bower('malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css') }}" rel="stylesheet">
<script type="text/javascript" src="{{ bower('malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js') }}"></script>

<!-- Magnific Popup core CSS file -->
<link rel="stylesheet" href="{{bower('magnific-popup/dist/magnific-popup.css')}}"> 
<script src="{{bower('magnific-popup/dist/jquery.magnific-popup.js')}}"></script> 

<!-- time ago -->
<script src="{{bower('jquery-timeago/jquery.timeago.js')}}" type="text/javascript"></script>

<!-- lazy load -->
<script src="{{bower('jquery.lazyload/jquery.lazyload.js')}}" type="text/javascript"></script>

<!-- Edit in place -->
<link href="{{bower('x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css')}}" rel="stylesheet"/>
<script src="{{bower('x-editable/dist/bootstrap3-editable//js/bootstrap-editable.min.js')}}"></script>

<!-- copy to clipboard -->
<script src="{{bower('zeroclipboard/dist/ZeroClipboard.min.js')}}"></script>

<!-- History -->
<script src="{{bower('history.js/scripts/bundled/html4+html5/native.history.js')}}"></script>

<!-- select 2 -->
<link href="{{bower('select2/select2-bootstrap.css')}}" rel="stylesheet"/>   
<script src="{{bower('select2/select2.js')}}"></script>

<!-- dropzone -->
<script src="{{bower('dropzone/dist/min/dropzone.min.js')}}"></script>
<link href="{{bower('dropzone/dist/min/dropzone.min.css')}}" rel="stylesheet"/>

<!-- bootstrap-hover-dropdown -->
<script src="{{bower('bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js')}}"></script>


<!-- Modules -->
<script src="{{ asset('assets/js/modules/spotComment.js') }}"></script>
<script src="{{ asset('assets/js/modules/comment.js') }}"></script>
<script src="{{ asset('assets/js/modules/spotVisit.js') }}"></script>
<script src="{{ asset('assets/js/modules/spotItinerary.js') }}"></script>
<script src="{{ asset('assets/js/modules/spotFavorite.js') }}"></script>
<script src="{{ asset('assets/js/modules/masonryMore.js') }}"></script>
<script src="{{ asset('assets/js/modules/searchBar.js') }}"></script>
<script src="{{ asset('assets/js/modules/googleLocationFinder.js') }}"></script>
<script src="{{ asset('assets/js/modules/googleMapper.js') }}"></script>
<script src="{{ asset('assets/js/modules/formStatus.js') }}"></script>
<script src="{{ asset('assets/js/modules/gotoMapLocation.js') }}"></script>
<script src="{{ asset('assets/js/modules/bugTracker.js') }}"></script>
<script src="{{ asset('assets/js/modules/imageUpload.js') }}"></script>

@if (isMobile())
	<script src="{{ asset('assets/js/modules/mobile.navigation.js') }}"></script>
	<script src="{{ asset('assets/js/modules/mobile.actions.js') }}"></script>
	<script src="{{ asset('assets/js/modules/mobile.carousel.js') }}"></script>
@endif

<script src="{{ asset('assets/js/modules/editable.text.js') }}"></script>
<script src="{{ asset('assets/js/modules/usersFinder.js') }}"></script>
<script src="{{ asset('assets/js/modules/userInfoPopover.js') }}"></script>

@if (Auth::check())
<script src="{{ asset('assets/js/modules/deleteAsset.js') }}"></script>
@endif