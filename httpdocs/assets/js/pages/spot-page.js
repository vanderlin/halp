$(document).ready(function($) {

	
	// ------------------------------------------------------------------------	
	var offset = {
		x:$(".profile-info-background").outerWidth() / 2,
		y:-100
	};

	// ------------------------------------------------------------------------
	function resizeSpotGoogleMap() {
		var pos = $(".info-col-wrapper").position().left;
		var innerW = $(".info-col-wrapper").width();
		var dw = innerW + pos;
		$(".info-col-wrapper .inner").width( dw );	
	}
	$(window).resize(function(event) {
		resizeSpotGoogleMap();
	});
	resizeSpotGoogleMap();


	// ------------------------------------------------------------------------
	$("#spot-map").googleMapper({
		locations:App.spots,
		infoWindowOffset:{
			x: -($("#spot-map").width()/2) + ($(".been-here-container").position().left +($(".been-here-container").width()/2)), 
			y:-100
		},
	});
	
	// ------------------------------------------------------------------------
	$('.been-here').spotVisit({
		onClick: function(event, data){
			
			$("#spot-map").googleMapper('closeInfoWindow');
			var spotid = $(this).data('id');
			var active = $(this).data('active');
			console.log("active:"+active);
			if(active == false) {
				$(this).spotComment({spotid:spotid});
			}
		},
		onUpdate: function(event, data) {
			$(".been-here-container .count h4").html(data.total);
		} 
	});

	// ------------------------------------------------------------------------
	$(".add-comment").click(function(event) {
		event.preventDefault();
		$(this).spotComment();
	}); 
	
	// ------------------------------------------------------------------------
	$(document).on('click', '[data-lo=comment-edit]', function(event) {
		event.preventDefault();
		var commentid = $(this).attr('data-id');
		var spotid = $(this).attr('data-spot-id');
		$(this).spotComment({commentID:commentid, spotid:spotid});
	});

	// ------------------------------------------------------------------------
	$(document).on('click', '[data-lo=comment-delete]', function(event) {
		event.preventDefault();
		var commentid = $(this).attr('data-id');
		var spotid =  $(this).attr('data-spot-id');
		if(!commentid || !spotid) {
			console.log("Missing data-id for lo-delete");
		}
		else {
			var really = confirm("Are you sure?");
			if(really) {
				$.ajax({
					url: "/comments/"+commentid,
					type: 'POST',
					dataType: 'json',
					data: {_method: 'DELETE'},
				})
				.done(function(evt) {
					console.log("success", evt);
					if(evt.status == 200) {
						$(".spot-comment-"+evt.data.id).fadeOut(500, function() {
							$(this).parent().remove();
						});
					}
				})
				.fail(function(evt) {
					console.log("error", evt);
				})
				.always(function() {
					console.log("complete");
				});
				
			}	
		}
	});	

});

