

(function($) {
    $.widget('lo.googleMapper', {
    
    	// -------------------------------------
    	cluster:null,
    	map:null,
    	
    	bounds:null,
    	infowindow:null,
    	canFireZoomEvent: false,

    	// -------------------------------------
    	options: {
    		centerOnLargestCluster:false,
    		zoomToLargestCluster:true,
    		zoom:16,
    		scrollwheel:false,
    		maxZoom:16,
    		disableDefaultUI:true,
    		infoWindowOffset:{x:0, y:0},
    		locations:null,
    		doCluster:false,
    	},

    	// -------------------------------------
    	clearLocations: function() {
    		if (this.cluster) {
    			this.cluster.clearMarkers();
    		};
    	},

    	// -------------------------------------
    	setLocations: function(data) {
    		this.clearLocations();
    		for (var i = 0; i < data.length; i++) {
    			this._addLocationToCluster(data[i]);
    		};
    	},

    	// -------------------------------------
    	getCluster: function() {
    		return this.cluster;
    	},

    	// -------------------------------------
    	getLocations: function() {
    		return this.options.locations;
    	},
    	
    	// -------------------------------------
    	getMap: function() {
    		return this.map;
    	},

    	// -------------------------------------
    	getMarkers: function() {
			return this.cluster.getMarkers();
    	},

    	// -------------------------------------
    	getMarkerFromSpotID: function(id) {
    		var markers = this.cluster.getMarkers();
    		for (var i = 0; i < markers.length; i++) {
    			if(markers[i].data && markers[i].data.id == id) return markers[i];
    		};
    		return null;
    	},

    	// -------------------------------------
    	getMarkerFromLocationID: function(id) {
    		var markers = this.cluster.getMarkers();
    		for (var i = 0; i < markers.length; i++) {
    			if(markers[i].location!==undefined && markers[i].location.id == id) return markers[i];
    		};
    		return null;
    	},

    	// -------------------------------------
    	gotoAndOpenLocation: function(options) {
    		options = options || {};
    		options.bounce = options.bounce===undefined ? false : options.bounce;
    		
    		var marker = null;
    		if(options.locationID!== undefined)  marker = this.getMarkerFromLocationID(options.locationID);
    		else if(options.spotID!== undefined) marker = this.getMarkerFromSpotID(options.spotID);

    		if(marker) {
    			if(this.map.getZoom()!=16) {
	            	this.map.setZoom(16);
	        	}
          		this.map.panTo(marker.position);    

				this.openInfoWindow(marker);          		
    		}
    		return marker;
    	},

    	// -------------------------------------
    	bounceMarker: function(options) {
    		var foundMarker = null;
    		var marker = null;
			var self = this;

    		if(options.locationID!== undefined)  marker = this.getMarkerFromLocationID(options.locationID);
    		else if(options.spotID!== undefined) marker = this.getMarkerFromSpotID(options.spotID);

    		this.closeInfoWindow();

    		if(marker) {
    			console.log(marker);
    			this.cluster.setMaxZoom(1);

    			if(this.map.getZoom()!=16) {
	            	this.map.setZoom(18);
	        	}
          		this.map.panTo(marker.position);    

          		if(options.openMarker === true) {
					setTimeout(function() {
						self.openInfoWindow(marker);
					}, 500);
				}
    			this.cluster.setMaxZoom(16);

    		}

    		return marker;
    	},

    	// -------------------------------------
    	getLargestCluster: function() {
    		var clusters = this.cluster.getClusters();
			var largest = 0;
			var index   = -1;
			for (var i = 0; i < clusters.length; i++) {
				var c = clusters[i];
				if(c.getSize() > largest) {
					largest = c.getSize();
					index = i;
				}
			};
			if(index != -1) {
				return clusters[index];
			}
			return null;
    	},

    	// -------------------------------------
    	_create: function() {
    		if(google) {
	    		var self = this;
	    	
	    		if(this.options.center !== undefined) {
	    			var coords = this.options.center.split(',');
	    			this.options.center = new google.maps.LatLng(coords[0], coords[1]);
	    		}
	    		var mapOptions = {
					scrollwheel:this.options.scrollwheel,
					zoom:this.options.zoom,
					minZoom:3,
					styles:googleMapsStyle,
				    disableDefaultUI:this.options.disableDefaultUI?this.options.disableDefaultUI:false,
			      	zoomControl: true,
				    zoomControlOptions: {
				    	style: google.maps.ZoomControlStyle.SMALL,
		    	        position: google.maps.ControlPosition.RIGHT_TOP
				    }
				};

	    		self.map = new google.maps.Map(this.element.context, mapOptions);
	    		self.bounds = new google.maps.LatLngBounds();
				self.infowindow = new google.maps.InfoWindow();

	    		self._setupCluster();

		   		google.maps.event.addListenerOnce(this.map, 'tilesloaded', function() { 
		   			console.log("Map Loaded");
	    			self._trigger( "onInit", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
				});

    		}	
    		else {
    			console.log("*** Missing Goole ` ***");
    		}
    		
    	},

    	// -------------------------------------
    	setInfoWindowOffset: function(offset) {
    		this.options.infoWindowOffset = offset;
    	},

    	// -------------------------------------
    	offsetCenter: function(latlng, offsetx, offsety, animated) {

			// latlng is the apparent centre-point
			// offsetx is the distance you want that point to move to the right, in pixels
			// offsety is the distance you want that point to move upwards, in pixels
			// offset can be negative
			// offsetx and offsety are both optional

			var scale = Math.pow(2, this.map.getZoom());
			var nw = new google.maps.LatLng(
				this.map.getBounds().getNorthEast().lat(),
				this.map.getBounds().getSouthWest().lng()
			);

			var worldCoordinateCenter = this.map.getProjection().fromLatLngToPoint(latlng);
			var pixelOffset = new google.maps.Point((offsetx/scale) || 0,(offsety/scale) ||0)

			var worldCoordinateNewCenter = new google.maps.Point(
				worldCoordinateCenter.x - pixelOffset.x,
				worldCoordinateCenter.y + pixelOffset.y
			);

			var newCenter = this.map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

			if(animated) {
				this.map.panTo(newCenter);				
			}
			else {
				this.map.setCenter(newCenter);
			}

		},

		// -------------------------------------
		closeInfoWindow: function() {
			if(this.infowindow) {
				this.infowindow.close();
			}
		},

    	// -------------------------------------
		openInfoWindow: function(marker) {
			
			//console.log(marker.data);

			if(this.options.activeIcon !== undefined) {

				var markers = this.getMarkers();
				for (var i = 0; i < markers.length; i++) {
					markers[i].setIcon(this._getIcon());
				};
				marker.setIcon(this.options.activeIcon);	
			}

			var self = this;
			var data = marker.data;
			console.log(marker);
			var location = marker.data.location==null ? marker.data : marker.data.location ; 
			var details = location.details ;
			var place = location.address;
			var fmt_address = location.formatted_address;
			
			var contentString =  '<div class="google-info-container" id="content">';
				contentString += '<div id="siteNotice">';
				contentString += '</div>';
		      	contentString += '<h5 id="firstHeading" class="title"><a href="'+data.url+'">'+data.name+'</a></h5>';
				contentString += '<div id="bodyContent" class="google-spot-info">';
				contentString += '<ul class="list-unstyled">';
				/*
				contentString += '<li>'+ place.street_number +' '+ place.street +'</li>';
				contentString += '<li>'+ place.city +' '+ place.state_short+', '+ place.zip +'</li>';
				*/
				contentString += '<li class="address">'+ fmt_address +'</li>';
				contentString += '<li><br></li>';
				
				if(details.formatted_phone_number !== undefined) {
					contentString += '<li class="phone-number">'+details.formatted_phone_number+'</li>';
				}

				if(details.website) {

					var webname = details.website.replace("http://www.", '');
					webname = webname.replace("http://", '');
					if(webname.charAt(webname.length-1)=="/") {
						webname = webname.substr(0, webname.length-1);
					}
					contentString += '<li class="website-link"><a href="'+details.website+'">'+webname+'</li>';
				}
				
				contentString += '</ul>';
				contentString += '</div>';
				contentString += '</div>';
			
			
			
				
  				self.infowindow.setContent(contentString);
				self.infowindow.open(self.map, marker);

				// self.map.setCenter(marker.getPosition());
				this.offsetCenter(marker.getPosition(), self.options.infoWindowOffset.x, self.options.infoWindowOffset.y, true);

				//var infoH = $(".google-info-container").height();

			
		},

		// -------------------------------------
		_getIcon: function(isVisit) {
			isVisit = isVisit || false;
			return this.options.icon || (isVisit ? '/assets/content/common/google/marker-single-visit.svg' : '/assets/content/common/google/marker-single.svg');
		},

    	// -------------------------------------
    	_makeMarker: function(latLng, data) {
    		
    		data = data || {};

    		var isVisit = data.isVisit || false;
			var icon = this._getIcon(isVisit);
			
			var self = this;
			var marker = new MarkerWithLabel({
				position: latLng,
				map:this.map,
				icon:icon,
				labelContent: data.order,
				labelAnchor: new google.maps.Point(13, 35),
				labelClass: "google-map-maker-labels",
				labelInBackground: false				
			});


			marker.data = data;
			
			var location = data;
			if(data.location !== undefined) {
				location = data.location;
			}

			marker.location = location;

			google.maps.event.addListener(marker, 'click', function(e) {
				self.openInfoWindow(this);
			});
			
			return marker;
		},

		// -------------------------------------
		_addLocationToCluster: function(data) {


			//, data, self.options.locations[i]


			
		
			// if(data.spot !== undefined ) {
			// 		data = data.spot;
			// }

			// if(data.spot !== undefined ) {
			// 	data = data.spot;
			// }
			// // didnt work
			// if(data === null) {
			// 	data = src;
			// }

			if(data.locationable_order !== undefined) {
				data.order = parseInt(data.locationable_order) + 1;
			}


			var latLng = new google.maps.LatLng(data.lat, data.lng);
			var marker = this._makeMarker(latLng, data);
				
			this.cluster.addMarker(marker);
			this.bounds.extend(latLng);	


		},

    	// -------------------------------------
    	_setupCluster: function() {


    		var self = this;

    		self.cluster = new MarkerClusterer(self.map, null, {
				zoomOnClick:true,
				minimumClusterSize:self.options.doCluster ? 2 : 100000000000000,
				averageCenter: true,
				styles: [
					{
						textColor:'#fff',
						height:33,
						width:33,
						url:'/assets/content/common/google/marker-33.svg'
					},
					{
						textColor:'#fff',
						height:56,
						width:56,
						url:'/assets/content/common/google/marker-56.svg'
					},
					{
						textColor:'#fff',
						height:66,
						width:66,
						url:'/assets/content/common/google/marker-66.svg'
					},
					{
						textColor:'#fff',
						height:78,
						width:78,
						url:'/assets/content/common/google/marker-78.svg'
					},
					{
						textColor:'#fff',
						height:90,
						width:90,
						url:'/assets/content/common/google/marker-90.svg'
					}
				]
			});

			var clusterInfoTimeOut;
		   	var cluserWindow;
		   	var clusterMarker = new google.maps.Marker({
		   		visible:false, 
		   		map:self.map, 
		   		maxZoom:self.options.maxZoom,
		   		// position: center, 
		   		clickable: false
		   	});


			if (this.options.center !== undefined) {
				console.log(self.cluster);
				self.cluster.map.setZoom(this.options.zoom);
				self.map.setCenter(this.options.center);
			}



		
		   	


		   	// we do this the first time to just find the largest cluster
		   	// we want to center the map to this cluster
		   	// -------------------------------------
		   	google.maps.event.addListenerOnce(this.map, 'bounds_changed', function() { 
				

		   		
				// we have just one marker
				if(self.options.locations!=null && self.options.locations.length == 1) {
					self.map.setZoom(12);
					self.openInfoWindow(self.cluster.getMarkers()[0]);
					// console.log('Single Marker');
				}
				else {

					var largest = self.getLargestCluster(); 

					if(largest) {
						if (self.options.zoomToLargestCluster) {
							var latlngbounds = new google.maps.LatLngBounds();
							var markers = largest.getMarkers();
							for (var i = 0; i < markers.length; i++) {
								latlngbounds.extend(markers[i].getPosition());
							};
							
							if(largest.getMarkers().length < 3) {
								// self.map.setZoom(12);
								self.map.setCenter(latlngbounds.getCenter());
							}
							else {
								self.map.setCenter(latlngbounds.getCenter());
								self.map.fitBounds(latlngbounds); 
							}
						}
						
			   		}
			   	}
			   	



			   	if(self.canFireZoomEvent) {

		   			self._trigger( "onZoomPanChange", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
		   		}


			});
	   	
			// -------------------------------------
		   	google.maps.event.addListener(this.map, 'zoom_changed', function() { 
		   		if(self.canFireZoomEvent) {
		   			self._trigger( "onZoomPanChange", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
		   			self._trigger( "onZoomChange", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
		   		}
		   		
		   	});

			// -------------------------------------
		   	google.maps.event.addListener(this.map, 'dragend', function() { 
		   		if(self.canFireZoomEvent) {
		   			self._trigger( "onZoomPanChange", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
		   			self._trigger( "onPanChange", null, { map:self.map, bounds:self.map.getBounds(), zoom:self.map.getZoom(), object:self} );
		   		}
		   		
		   	});

		   	// -------------------------------------
			google.maps.event.addListener(self.map, 'click', function (c) {
				self._trigger( "onMapClick", null, c);
			});



		   	// -------------------------------------
			google.maps.event.addListener(self.cluster, 'mouseover', function (c) {
			   	
		   		self.activeC = c;
		   		if(cluserWindow) {
		   			cluserWindow.close();
		   		}
		   		
		   		cluserWindow = new google.maps.InfoWindow({pixelOffset:{width:0, height:-20}});
  				var p = new google.maps.LatLng(c.getCenter().lat(), c.getCenter().lng());
  				clusterMarker.setPosition(p);

  				var markers = c.getMarkers();

  				var maxMarkers = 9;
  				var contentString =  '<div class="google-info-container" id="content">';
					contentString += 	'<div id="siteNotice"></div>';
					// contentString += 		'<h5 id="firstHeading" class="title">Location of Cluster</h5>';
					contentString += 		'<div id="bodyContent" class="cluster-list-info">';
					contentString += 		'<ul class="cluster-list list-unstyled">';
												for (var i = 0; i < Math.min(maxMarkers, markers.length); i++) {
								  					if(markers[i].data) {
								  						var spot = markers[i].data;
														contentString += '<li><a href="'+spot.url+'">'+spot.name+'</a></li>';
								  					}
								  				};
								  				if(markers.length>maxMarkers) {
								  					contentString += '<li><small>and '+(markers.length-maxMarkers)+' more</small></li>';
								  				}
					contentString += 		'</ul>';
					contentString += 	'</div>';
					contentString += '</div>';

				
				cluserWindow.setContent(contentString)
				cluserWindow.open(self.map, clusterMarker);
			});
			$(document).on('mouseover', '.google-info-container', function(event) {
				if(cluserWindow) {
					clearTimeout(clusterInfoTimeOut);
				}
			});
			
			// -------------------------------------
			$(document).on('mouseleave', '.google-info-container', function(event) {
				
				if(cluserWindow) {
					clearTimeout(clusterInfoTimeOut);
					clusterInfoTimeOut = setTimeout(function() {
						cluserWindow.close();	
					}, 1200);
		   		}
			});

			// -------------------------------------
			google.maps.event.addListener(self.cluster, 'mouseout', function (c) {
				if(cluserWindow) {
					clearTimeout(clusterInfoTimeOut);
					clusterInfoTimeOut = setTimeout(function() {
						cluserWindow.close();	
					}, 1200);
		   		}
			});

			// -------------------------------------
			google.maps.event.addListener(self.cluster, 'click', function (c) {
				if(cluserWindow) cluserWindow.close();
			});

			if(self.options.locations) {

				// console.log('locations', self.options.locations);
				for (var i = 0; i < self.options.locations.length; i++) {
					self._addLocationToCluster(self.options.locations[i]);
				}

				// *******************************
				// WIP
				// *******************************
				setTimeout(function() {
				self.map.setZoom(3);
					self.cluster.fitMapToMarkers();
				}, 500);

			}
			else {
				console.log('No locations');
				console.log(self.options.center);
				self.map.setZoom(4);
				self.map.setCenter(self.options.center);
				
			}			

			setTimeout(function() {
				self.canFireZoomEvent = true;
			}, 2000);


		}

    });
}(jQuery));