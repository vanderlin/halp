$(document).ready(function($) {
	
	$("#itinerary-map").googleMapper({
		locations:App.spots,
		icon:'/assets/content/common/icons/itinerary-marker.svg',
		activeIcon:'/assets/content/common/icons/itinerary-marker-active.svg',
	});

	$(".share-btn").popover({
		placement:'top',
		html:true,
		template:'<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
		content:'\
		<ul class="list-unstyled share-list">\
			<li>\
				<div class="input-group">\
					<input type="text" class="form-control url-input" value="'+$(".share-btn").data('share')+'">\
					<span class="input-group-btn">\
						<button class="btn btn-default url-copy-clipboard" type="button" data-clipboard-text="'+$(".share-btn").data('share')+'">copy</button>\
					</span>\
					</div>\
			</li>\
		</ul>'
	})
	.on('shown.bs.popover', function() {
		var client = new ZeroClipboard( $(".share-list .url-copy-clipboard") );
		$(".share-list .url-input").focus(function() { $(this).select(); } );
	});
	

	
});

