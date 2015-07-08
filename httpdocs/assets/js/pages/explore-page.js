$(document).ready(function($) {


		// -------------------------------------
		var offset = {
			x:$(".profile-info-background").outerWidth() / 2,
			y:-100
		};

		// -------------------------------------
		$("#explore-map").googleMapper({
			infoWindowOffset:offset,
			zoomToLargestCluster:false,
			centerOnLargestCluster:true,
			locations:App.spots,
			onZoomChange:function(e) {

				
						
			}
		});
		
		// -------------------------------------
		function resizeSpotGoogleMap() {
		
			var offset = {
				x:$(".profile-info-background").outerWidth() / 2,
				y:-100
			};

			$("#explore-map").googleMapper('setInfoWindowOffset', offset);
		}

		// -------------------------------------
		$(window).resize(function(event) {
			resizeSpotGoogleMap();
		});
		resizeSpotGoogleMap();	

});

