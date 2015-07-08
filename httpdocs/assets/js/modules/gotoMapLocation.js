(function($) {
  $.widget('lo.gotoMapLocation', {

    // -------------------------------------
    options: {
      trigger:'click'
    },

    gotoLocation: function() {

        var $target     = $(this.element.data('target'));
        var locationID  = this.element.data('location-id') || null;
          
        if($target.length) {
          
          // var pos = new google.maps.LatLng(location[0], location[1]);
          // var map = $target.googleMapper('getMap');
          
          // if(map.getZoom()!=16) {
          //   map.setZoom(16);
          // }
          // map.panTo(pos);        

          

          if(locationID) {
            var marker = $target.googleMapper('bounceMarker', {locationID:locationID, openMarker:true});

            
            // var marker = $target.googleMapper('bounceMarker', {locationID:locationID, openMarker:true});
            // if(marker) {
            //   $target.googleMapper('bounceMarker'
            // }
          }
          this._trigger( "onClick", null, {id:locationID, marker:marker});
      }
      else {
        console.log('Missing map target');
      }
    },

    // -------------------------------------
    _create: function() {
      var $this     = this.element;
      var self      = this;
      
      this.element.css('cursor', 'pointer');

      if(this.options.trigger == 'click') {
        this.element.click(function(e) {
          e.preventDefault();
          self.gotoLocation();
        });
      }
      else if(this.options.trigger == 'hover') {
        this.element.mouseenter(function(e) {
          e.preventDefault();
          self.gotoLocation();
        });
      }

    },

  });

}(jQuery));















