



(function($) {
    $.widget('lo.googleLocationFinder', {

    	// -------------------------------------
    	options: {
            searchURL:'/api/search/query',
            shouldCheckForAvailability:true,
            showAddButton:true,
        	target:null,
            height:null,
            iconPath:'/assets/content/common/google/marker-single.svg',
            delay:200,
            useLocalDB:true,
            canAddSpots:false,
    	},

        _spot:null,
        _placesService:null,
        _predictions:[],
        _results:null,
        _focusOutDelay:null,

        // -------------------------------------

        getStaticMapImage: function(pos) {
            
        },
        
        // -------------------------------------
        _makeMarker: function(latLng, data) {
            var marker = new google.maps.Marker({
                position: latLng,
                map:this.map,
                icon:this.options.iconPath
            });
            //bounds.extend(latLng);
            // self.markers.push(marker);
            // marker.data = data;
            // addMarkClickEvent(marker);

            return marker;
        },

        // -------------------------------------
        _addLocation: function(place, element) {
            this._trigger( "onAddLocation", null, { place:place, element:element} );
        },

        // -------------------------------------
        _addSpot: function(spot, element) {
            this._trigger( "onAddSpot", null, { spot:spot, element:element} );
        },

        // -------------------------------------
        _openInfoWindow: function(content) {
            if(this.infoWindow==null) this.infoWindow = new google.maps.InfoWindow();
            this.infoWindow.setContent(content);
            this.infoWindow.open(this.map, this.marker);
        },

        // -------------------------------------
        _getPaceWindowContent: function(place) {
          var content =   '<div class="google-content text-center">\
                            <h4>'+place.name+'</h4>\
                            <div class="address">'+place.formatted_address+'</div>';
            content +=      this.options.showAddButton ? '<div><a href="#add-location" class="add-location btn btn-default btn-xs">Add Location</a></div><br>' : '';
            content +=  '</div>';
            return content;
        },

         // -------------------------------------
        _getSpotWindowContent: function(spot) {
          var content =   '<div class="google-content text-center">\
                            <h4><a href="'+spot.url+'" target="_blank">'+spot.name+'</a></h4>\
                            <div class="address">'+spot.location.formatted_address+'</div>';
            content +=      this.options.showAddButton ? '<div><a href="#add-spot-location" class="add-spot-location btn btn-default btn-xs">Add Spot</a></div><br>' : '';
            content +=  '</div>';
            return content;
        },

        // -------------------------------------
        _getNoAvialablePaceWindowContent: function(place, spot) {
            if(this.options.getNoAvialablePaceWindowContent && typeof this.options.getNoAvialablePaceWindowContent == 'function') {
                return this.options.getNoAvialablePaceWindowContent(place, spot);
            }
            var content =   '<div class="google-content text-center">\
                                <h4><a href="/spots/'+spot.id+'">'+place.name+'</a></h4>\
                                <div class="address"><a href="/spots/'+spot.id+'">'+place.name+'</a> has already been created as a spot</div>';
                content +=      '<div><a href="#add-spot-location" class="add-spot-location btn btn-default btn-xs">Add Spot</a></div><br>';
                content +=  '</div>';
            return content;  
        },
        // -------------------------------------
        /*_shouldShowInfoWindow: function(place) {
            if(typeof this.options.shouldShowInfoWindow == 'function') {
                return this.options.shouldShowInfoWindow(place);
            }
            else if(typeof this.options.shouldShowInfoWindow == 'boolean') {
                return this.options.shouldShowInfoWindow;
            }
            return true;
        },*/

        _checkForAvailability: function(options) {
            $.ajax({
                url: '/api/spots/available',
                type: 'POST',
                dataType: 'json',
                data: {place_id: options.place.place_id},
            })
            .done(function(e) {
                if(e.status == 200) {
                    if(e.available) {
                        if(options.onAvailable) options.onAvailable(e, options.place);
                    }
                    else {
                        if(options.onNotAvailable) options.onNotAvailable(e, options.place);   
                    }
                }
            })
            .fail(function(e) {
                console.log("error _checkForAvailability", e);
            })
        },

        // -------------------------------------
        _getDetails: function(place) {
            var self = this;
            if(this._placesService == null) this._placesService = new google.maps.places.PlacesService(this.map);
            this._placesService.getDetails({reference: place.reference}, function(details, status){
                if(details){
                    console.log(details.geometry.location.toString());
                }
                self._setMapWithPlace(details);
                self._trigger( "onNewLocation", null, { place:details } );
            });
        },

        // -------------------------------------
        _search: function(term) {
            var self = this;
            if(this.options.useLocalDB) {
                $.ajax({
                    url: this.options.searchURL+'/'+term,
                    type: 'GET',
                    dataType: 'json',
                })
                .always(function(e) {
                    console.log(e);
                    if(e.status == 200 && e.results) {
                        self._createResultsList(e.results);
                    }
                });
                
            }
            /*
            this.service.getPlacePredictions({ input: term }, function(predictions, status) {
                if (status != google.maps.places.PlacesServiceStatus.OK) {
                    console.log(status);
                    return;
                }
                
                self._checkPredictions(predictions);
            });*/
        },

        // -------------------------------------
        _checkPredictions: function(predictions) {
            var self = this;
            var ids = $(predictions).map(function(i, e) { return e.place_id;}).get();
            $.ajax({
                url: '/api/locations/check',
                type: 'GET',
                dataType: 'json',
                data: {ids: ids},
            })
            .always(function(e) {
                
                if(e.data.length) {
                    for (var i = 0; i < e.data.length; i++) {
                        var item = e.data[i];
                        for (var j = 0; j < predictions.length; j++) {
                            if(item.place_id == predictions[j].place_id) {
                                predictions[j].spot = item.spot;
                            }
                        };
                    };
                }

                self._createResultsList(predictions);
            });
            
        },

        // -------------------------------------
        _createResultsList: function(results) {
            console.log(results);
            var self = this;
            var html = '<ul class="list-unstyled location-finder-results list-group">';
                var spotsList = '';
                var locationsList = '';

               for (var i = 0; i < results.spots.length; i++) {
                    var spot = results.spots[i];
                    var item = '\
                    <li class="list-group-item spot">\
                        <i class="icon fa fa-dot-circle-o "></i>\
                        <small class="pull-right text-muted">Spot Already Exist \
                        <a href="'+spot.url+'"><i class="fa fa-external-link"></i></a>\
                        </small>\
                        '+(this.options.canAddSpots?'<a class="spots-result" href="#'+spot.id+'" data-id="'+spot.id+'">'+spot.name+'</a>':spot.name)+'\
                        <br><small class="results-address text-muted">'+spot.location.formatted_address+'</small>\
                        </li>';
                    spotsList += item;
               };



               for (var i = 0; i < results.locations.length; i++) {
                    var location = results.locations[i];
                    var item = '\
                        <li class="list-group-item">\
                            <i class="icon fa fa-map-marker"></i>\
                            <a class="location-result" href="#'+location.place_id+'" data-id="'+location.place_id+'">'+location.name+'</a>\
                        </li>';
                        locationsList += item;
               };
                /*
                for (var i = 0, prediction; prediction = predictions[i]; i++) {
                    
                    
                    if(prediction.spot !== undefined) {
                        var spot = prediction.spot;
                        var item = '\
                        <li class="list-group-item spot">\
                            <i class="fa fa-dot-circle-o "></i>\
                            <small class="pull-right text-muted">Spot Already Exist \
                            <a href="'+spot.url+'"><i class="fa fa-external-link"></i></a>\
                            </small>\
                            <a href="">'+spot.name+'</a>\
                            </li>';
                        spotsList += item;
                    }
                    
                    else {
                        var item = '\
                        <li class="list-group-item">\
                            <i class="fa fa-map-marker"></i>\
                            <a class="location-result" href="#'+prediction.place_id+'" data-id="'+prediction.place_id+'">'+prediction.description+'</a>\
                        </li>';
                        locationsList += item;
                    }                    
                };
            */
            if(spotsList!='')       html += '<li class="list-group-item title"><h6>Spots</h6></li>'+spotsList;
            if(locationsList!='')   html += '<li class="list-group-item title"><span class="pull-right"><img src="/assets/google/powered-by-google-on-white.png"></span><h6>Other Locations</h6></li>'+locationsList;

            html += '</ul>';

            var $list = $(html);
                $list.width(this.element.parent().width());

            this._results = results;
            this.element.parent().find('.location-finder-results').remove();
            this.element.parent().append($list);


            $list.find('.location-result').click(function(e) {
                self._locationClicked(e);
            });
            $list.find('.spots-result').click(function(e) {
                self._spotClicked(e);
            });

        },
        
        // -------------------------------------
        _locationClicked: function(e) {
            var $elem = $(e.target);
            var self  = this;
            e.preventDefault();
            var place = self._getPlaceFromPredictionsList($elem.data('id'));
            if(place) {
                self._getDetails(place);
                self._hideResults();
            }
        },

        // -------------------------------------
        _spotClicked: function(e) {
            var $elem = $(e.target);
            var self = this;
            e.preventDefault();
            var spot = self._getSpotFromSpotList($elem.data('id'));
            if(spot) {
                self._setMapWithSpot(spot);
                self._hideResults();
            }
        },

        // -------------------------------------
        _hideResults: function() {
            this.element.parent().find('.location-finder-results').hide();
        },

        // -------------------------------------
        _showResults: function() {
            this.element.parent().find('.location-finder-results').show();
        },

        // -------------------------------------
        _clearResults: function() {
            this.element.parent().find('.location-finder-results').remove();
        },

        // -------------------------------------
        _setMapWithPlace: function(place) {
            var self = this;
            console.log(place);
            self.map.setCenter(place.geometry.location);
            if(self.marker) {
                self.marker.setPosition(place.geometry.location);
            }
            self._openInfoWindow(self._getPaceWindowContent(place));
            this.place = place;
            this.element.val(place.name);
        },

        // -------------------------------------
        _setMapWithSpot: function(spot) {
            var self = this;
            var pos = new google.maps.LatLng(spot.location.lat, spot.location.lng);
            self.map.setCenter(pos);
            if(self.marker) {
                self.marker.setPosition(pos);
            }
            self._spot = spot;
            self._openInfoWindow(self._getSpotWindowContent(spot));
        },

        // -------------------------------------
        _getSpotFromSpotList: function(id) {
            var spots = this._results.spots;
            for (var i = 0, spot; spot = spots[i]; i++) {
                if(spot.id == id) {
                    return spot;
                }
            }
            return null;
        },

        // -------------------------------------
        _getPlaceFromPredictionsList: function(id) {
            var predictions = this._results.predictions;
            for (var i = 0, prediction; prediction = predictions[i]; i++) {
                if(prediction.place_id == id) {
                    return prediction;
                }
            }
            return null;
        },


    	// -------------------------------------
		_create: function() {
			
            var self = this;
                

            // -------------------------------------
            google.maps.event.addDomListener(window, 'load', function() {
                
                self.service = new google.maps.places.AutocompleteService();
                var timer;
                var prevVal;
                var listIsFocused  = false;
                var count = 0;
                
                self.element.keyup(function(event) {
                    
                    var key = event.which;
                    // 38 == up
                    // 40 == down
                    if(key == 38 || key == 40 || key == 13) {
                        var items = self.options.list.find('li:not(.section)');
                        if(items.length>0) {
                            if(key == 13) {
                                document.location = $(items[count]).find('a').attr('href');
                                return; 
                            }
                            else if(key == 40 && count<items.length-1) {
                                if(listIsFocused == true) {
                                    count ++;
                                }
                                var item = $(items[count]);
                                updateSelected(count, items);
                            }
                            else if(key == 38 && count>0) {
                                count --;
                                updateSelected(count, items);
                            }
                        }
                    }
                    else {              
                        var val = $(this).val();                
                        clearTimeout(timer);
                        timer = setTimeout(function() {

                            if(prevVal != val && val.length>0) {
                                self._search(val);
                                count = 0;
                            }
                            prevVal = val;

                        }, self.options.delay); 
                    }
                    
                });

                // service.getQueryPredictions({ input: 'pizza near' }, self._onPrediction);
                // return;
                // self.searchBox = new google.maps.places.SearchBox(self.element.context);

                var $map = $("#"+self.options.map);
                if(self.options.map && $map.length) {
                    
                    if(self.options.height) {
                        $map.height(self.options.height);
                    }

                    self.defaultPos = self.options.spot ? new google.maps.LatLng(self.options.spot.lat, self.options.spot.lng) : new google.maps.LatLng(42.3584865, -71.06009699999998);
                    
                    self.map = new google.maps.Map(document.getElementById(self.options.map), {
                        zoom:18,
                        styles:googleMapsStyle,
                        center:self.defaultPos,
                    });
                    if(self.options.spot) {
                        var spot = self.options.spot;
                        self.marker = self._makeMarker(self.defaultPos);
                            
                        var content = self._getPaceWindowContent({name:spot.name, formatted_address:spot.location.formatted_address});

                        self._openInfoWindow(content);
                        self.map.setZoom(14);
                    }
                    else {
                        self.marker = self._makeMarker(self.defaultPos);
                        self.map.setZoom(14);
                    }

                }
                
                /*
                // new place....
                google.maps.event.addListener(self.searchBox, 'places_changed', function() {
                    
                    var places = self.searchBox.getPlaces();
                    if (places.length == 0) return;
                    var place = places[0];
                        self.place = place;
                    if(self.options.shouldCheckForAvailability) {
                        self._checkForAvailability({
                            place:place, 
                            onAvailable:function(evt, place) {
                                console.log('onAvailable');
                                self._trigger( "onAvailable", null, { event:evt, place:place } );
                                self.map.setCenter(place.geometry.location);
                                if(self.marker) {
                                    self.marker.setPosition(place.geometry.location);
                                }
                                self._openInfoWindow(self._getPaceWindowContent(place));
                                self._trigger( "placeChanged", null, { place:place } );

                            },
                            onNotAvailable:function(evt, place) {
                                
                                self.spotFound = evt.spot;

                                self._trigger( "onNotAvailable", null, { event:evt, place:place } );
                                self.map.setCenter(place.geometry.location);
                                if(self.marker) {
                                    self.marker.setPosition(place.geometry.location);
                                }
                                self._openInfoWindow(self._getNoAvialablePaceWindowContent(place, evt.spot));
                                self._trigger( "placeChanged", null, { place:place } );

                            }
                        });
                    }
                    else {
                        self.map.setCenter(place.geometry.location);
                        if(self.marker) {
                            self.marker.setPosition(place.geometry.location);
                        }
                        self._openInfoWindow(self._getPaceWindowContent(place));
                        self._trigger( "placeChanged", null, { place:place } );
                    }     

                });*/

            });
            
            this.element.focusin(function(event) {
                if(self._results) {
                    self._showResults();
                }
            });
            this.element.focusout(function(event) {
                var val = $(this).val();
                if(self._focusOutDelay != null) {
                    clearTimeout(self._focusOutDelay);
                    self._focusOutDelay = null;
                }
                self._focusOutDelay = setTimeout(function() {
                    if(self._results) {
                        if(val.length == 0) {
                            self._clearResults();
                        }
                        else {
                            self._hideResults();    
                        }
                    }
                    clearTimeout(self._focusOutDelay);
                    self._focusOutDelay = null;    
                }, 300);

                
            });
           
            this.element.bind('keypress keydown keyup', function(e){
                if(e.keyCode == 13) { e.preventDefault(); }

                if($(this).val().length == 0) {
                    self._clearResults();
                }
            });

            $(document).on('click', '.add-location', function(event) {
                event.preventDefault();
                self._addLocation(self.place, this);
            });

            $(document).on('click', '.add-spot-location', function(event) {
                event.preventDefault();
                self._addSpot(self._spot, this);
            });

    	},
    	

    });

}(jQuery));















