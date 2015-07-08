App.makePDF = function() {




	var pdf = new jsPDF();
	pdf.line(20, 20, 60, 20); // horizontal line
		
	pdf.setLineWidth(0.5);
	pdf.line(20, 25, 60, 25);

	pdf.setLineWidth(1);
	pdf.line(20, 30, 60, 30);

	pdf.setLineWidth(1.5);
	pdf.line(20, 35, 60, 35);

	pdf.setDrawColor(255,0,0); // draw red lines

	pdf.setLineWidth(0.1);
	pdf.line(100, 20, 100, 60); // vertical line

	pdf.setLineWidth(0.5);
	pdf.line(105, 20, 105, 60);

	pdf.setLineWidth(1);
	pdf.line(110, 20, 110, 60);

	pdf.setLineWidth(1.5);
	pdf.line(115, 20, 115, 60);

	function getBase64Image(img) {
	    // Create an empty canvas element
	    var canvas = document.createElement("canvas");
	    canvas.width = img.width;
	    canvas.height = img.height;
	 
	    // Copy the image contents to the canvas
	    var ctx = canvas.getContext("2d");
	    ctx.drawImage(img, 0, 0);
	 
	    // Get the data-URL formatted image
	    // Firefox supports PNG and JPEG. You could check img.src to
	    // guess the original format, but be aware the using "image/jpg"
	    // will re-encode the image.
	    var dataURL = canvas.toDataURL("image/png");
	 
	    return dataURL;//.replace(/^data:image\/(png|jpg);base64,/, "");
	}

	for (var i = 0; i < itinerary.locations.length; i++) {
		var location = itinerary.locations[i];
		if(location.spot!==null) {
			var url = location.spot.thumbnail_base+'/s500';
			var img = new Image();
				img.src = url;
				var y = 0;
				img.onload = function(e) {
					var data = getBase64Image(this);
					
					pdf.addImage(data, 'PNG', 10, 30, 180, 160);
					y += 500;
					console.log(y);
				}
			
		}
		// var imgData = location.spot !== null ? location.spot.image_uri : location.image_uri;
		
		// var mime = location.spot !== null ? location.spot.image_mime_type.split('/')[1] : location.image_mime_type.split('/')[1];
		// console.log(mime);
		// pdf.addImage(imgData, mime, 15, 40, 180, 160);
		
	};
	// pdf.addImage(imgData, 'JPEG', 15, 40, 180, 160);

	setTimeout(function() {
		var string = pdf.output('datauristring');	
		$('iframe').attr('src', string);
	}, 2000);
	

	

	$('iframe').height($(document).height());



}