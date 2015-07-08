$(document).ready(function($) {


		// -------------------------------------
		var offset = {
			x:$(".profile-info-background").outerWidth() / 2,
			y:-100
		};

		// -------------------------------------
		$("#user-map").googleMapper({
			infoWindowOffset:offset,
			defaultLocation:defaultLocation,
			locations:App.spots
		});
		
		// -------------------------------------
		function resizeSpotGoogleMap() {
			
			// var pos = $(".info-col-wrapper").position().left;
			// var innerW = $(".info-col-wrapper").width();
			// var dw = innerW + pos;
			
			// // if(offx == null) {
			// // 	offx = $(".spot-info .circle-map-container").offset().left;
			// // 	console.log(offx);
			// // }
			// $(".info-col-wrapper .inner").width( dw );	

			var offset = {
				x:$(".profile-info-background").outerWidth() / 2,
				y:-100
			};

			$("#user-map").googleMapper('setInfoWindowOffset', offset);
		}

		// -------------------------------------
		$(window).resize(function(event) {
			resizeSpotGoogleMap();
		});
		resizeSpotGoogleMap();	

		
		


});

