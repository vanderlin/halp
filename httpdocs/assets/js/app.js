

function prevent_default(e) {
    e.preventDefault();
}
function disable_scroll() {
    $(document).on('touchmove', prevent_default);
}
function enable_scroll() {
    $(document).unbind('touchmove', prevent_default)
}

var Utils = {
  stringToBool: function(string){
    switch(string.toLowerCase()){
      case "true": case "yes": case "1": return true;
      case "false": case "no": case "0": case null: return false;
      default: return Boolean(string);
    }
  },
  
  nl2br: function(str) {
    return str.replace(/\n|\r\n|\r/g, '<br>');
  },
  br2nl: function(str) {
    return str.replace(/<br\/>|<br>/g, '\n');
  },

  isIOS: function() {
    return !!navigator.platform.match(/iPhone|iPod|iPad/);
  },
  
  moveCursorToEnd: function(el) {
        el.focus();
        el = el[0];
        if (typeof el.selectionStart == "number") {
            el.selectionStart = el.selectionEnd = el.value.length;
        } else if (typeof el.createTextRange != "undefined") {
            el.focus();
            var range = el.createTextRange();
            range.collapse(false);
            range.select();
        }
    },
}

// ------------------------------------------------------------------------
var App = (function() {
	
  var _infoDelay = 300;
  
	var self = {

  };

	self.masonry = [];

  // ------------------------------------------------------------------------
  self.getMasonry = function(id) {
      for (var i = 0; i < self.masonry.length; i++) {
        var mid = '#'+$(self.masonry[i].container).attr('id');
        if(mid == id) {
          return self.masonry[i].masonry;
        }
      }
      return null;
    }

    
    // ------------------------------------------------------------------------
    self.get_static_style = function(styles) {
      var result = [];
      styles.forEach(function(v, i, a) {
          
          var style='';
          if( v.stylers ) { // only if there is a styler object
              if (v.stylers.length > 0) { // Needs to have a style rule to be valid.
                  style += (v.hasOwnProperty('featureType') ? 'feature:' + v.featureType : 'feature:all') + '|';
                  style += (v.hasOwnProperty('elementType') ? 'element:' + v.elementType : 'element:all') + '|';
                  v.stylers.forEach(function(val, i, a){
                      var propertyname = Object.keys(val)[0];
                      var propertyval = val[propertyname].toString().replace('#', '0x');
                      style += propertyname + ':' + propertyval + '|';
                  });
              }
          }
          result.push('style='+encodeURIComponent(style));
      });
      
      return result.join('&');
    }


    // ------------------------------------------------------------------------
		self.init = function() {
      
      console.log("App Init");
      
      // init window resize 
      self.windowResize();

      /*
      var userMenuHoverOutDelay = null; 
      $(".user-dropdown").hover(
        function() {
          if(userMenuHoverOutDelay) {
            clearTimeout(userMenuHoverOutDelay);
            userMenuHoverOutDelay = null;
          }
          $('.user-dropdown', this).stop( true, true ).fadeIn("fast");
          $(this).toggleClass('open');
          $('b', this).toggleClass("caret caret-up");                
        },
        function() {
          var self = this;
          if(userMenuHoverOutDelay) {
            clearTimeout(userMenuHoverOutDelay);
            userMenuHoverOutDelay = null;
          }
          userMenuHoverOutDelay = setTimeout(function() {
            $('.user-dropdown', self).stop( true, true ).fadeOut("fast");
            $(self).toggleClass('open');
            $('b', self).toggleClass("caret caret-up");                  
          }, 300);
          
        }
      );*/


      // scroll
      // ------------------------------
			$('.scroll-to-anchor').each(function(i, e) {
				var target = $(e).attr('data-scroll-to');
				$(e).click(function(event) {
					self.scrollTo(target);
				});
			});

      // popover
      // ------------------------------
      // $("[data-toggle=popover]").popover();

      // masonry
      // ------------------------------

      $(".masonry-container").each(function(i, e) {
        
        var $container = $(this);
        
        // $(this).find('img.lazy').lazyload({
        //   effect : "fadeIn"
        // });

      /*$("img.lazy").lazyload({
        effect : "fadeIn"
      });
      $('img.lazy').load(function() {
        masonry_update();
      });
      function masonry_update() {     
        var $works_list = $('#works_list');
        $works_list.imagesLoaded(function(){
            $works_list.masonry({
                itemSelector: '.work_item',　
                isFitWidth: true,　
                columnWidth: 160
            });
        });
     }*/


        $container.imagesLoaded(function() {          
          var $container = $(this.elements[0]).masonry();
          self.masonry.push({container:this.elements[0], masonry:$container});
        });

      });
      



  
      // ------------------------------
      // scroll to hash
      /*setTimeout(function() {
        var hash = document.location.hash;
        
        if(hash && $(hash).length) {
          self.scrollTo(hash);
        }
      }, 300);*/




		}
		

    // ------------------------------------------------------------------------
		self.scrollTo = function(target, speed, callback) {
			target = $(target);
      $('body').animate({ scrollTop: target.offset().top - 100 }, speed||500, function(e) {
        if(callback) {
          callback(e)
        }
      });
		}


    // ------------------------------------------------------------------------
    self.windowResize = function(e) {
      
      if($(".main-navbar .container").length) {
        var offset = $(".main-navbar .container").offset().left;
        var containerWidth = $(".main-navbar .container").width();
        var gutter = 30;
        
        $( "div[class*='lo-hero-edge-bleed']" ).each(function(i, e) {
          var $elem = $(this);
          
          for (var i = 0; i < $(this).context.classList.length; i++) {
            var c = $(this).context.classList[i];
            if(c.search('col-')!=-1) {
              var numCols = parseInt(c.match(/\d+/), 10);
              var pct = numCols / 12.0;
              var w = ((containerWidth * pct) + offset) - 30;
              $(this).width(w);
            }
          }
        });
      }

      
      // $('.footer').css({
      //   position:'absolute', 
      //   top:($(document).height()-47)+'px', 
      //   width:'100%'
      // });
    }

  // ------------------------------------------------------------------------
  self.setupUserInfo = function() {
     $('.user-image').popover({
        trigger: 'manual', 
        placement: 'top',
        container:'body',
        html:true, 
        template:'<div class="popover info-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
        content:function() {
          return '\
          <ul class="list-unstyled popover-list user-image-name">\
            <li><a href="'+$(this).attr('href')+'">'+$(this).data('name')+'</a><div class="location"><small class="text-muted">'+$(this).data('location')+'</small></div></li>\
          </ul>'
        }
      })
      .on("mouseenter", function() {
        var _this = this;
        var timer = $(this).data('timer');
        if (timer == undefined) {
          timer = setTimeout(function() {
            $(_this).popover("show");
            var $popover = $('#'+$(_this).attr('aria-describedby'));
            $popover.on("mouseleave", function() {
              $(_this).popover('hide');
            });
            clearTimeout(timer);
            $(_this).data('timer', null);  
          }, _infoDelay);
          $(this).data('timer', timer);
        }
      })
      .on("mouseleave", function() {
        var _this = this;
        var timer = $(this).data('timer');
        if(timer) {
          clearTimeout(timer);
          $(_this).data('timer', null);  
        }

        setTimeout(function() {
          if (!$(".popover:hover").length) {
            $(_this).popover("hide")
          }
      }, 100);
    });
  }

  self.loadModules = function() {

    // close popovers when clicked outside...
    $('body').on('click', function (e) {
        $('[data-toggle="popover"]').each(function () {
            //the 'is' for buttons that trigger popups
            //the 'has' for icons within a button that triggers a popup
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                var removeOnHide = $(this).data('remove-on-hide');
                if(removeOnHide === undefined) removeOnHide = true;
                if(removeOnHide) $(this).popover('destroy');
                else $(this).popover('hide');
            }
        });
    });



    $('[data-activity-more]').popover({
      trigger: 'manual', 
      placement: 'top',
      container:'body',
      html:true, 
      template:'<div class="popover activity-popover info-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
      content:function() {
        var str = $(this).data('more-content');
        return decodeURIComponent((str + '').replace(/\+/g, '%20'));
      },
    })
    .on("mouseenter", function() {
        var _this = this;
        var timer = $(this).data('timer');
        if (timer == undefined) {
          timer = setTimeout(function() {
            $(_this).popover("show");
            var $popover = $('#'+$(_this).attr('aria-describedby'));
            $popover.on("mouseleave", function() {
              $(_this).popover('hide');
            });
            clearTimeout(timer);
            $(_this).data('timer', null);  
          }, _infoDelay);
          $(this).data('timer', timer);
        }
    })
    .on("mouseleave", function() {
        var _this = this;
        var timer = $(this).data('timer');
        if(timer) {
          clearTimeout(timer);
          $(_this).data('timer', null);  
        }

        setTimeout(function() {
          if (!$(".popover:hover").length) {
            $(_this).popover("hide")
          }
        }, 100);
    });

   this.setupUserInfo();
    
    

    $('[data-toggle="tooltip"]').tooltip()

    $('.lo-bug-tracker').bugTracker();
    $('.lo-spot-comment').spotComment();
    $('.lo-comment').comment();
    $('.lo-spot-favorite').spotFavorite();
    

    $('.lo-spot-visit').spotVisit();
    $('.lo-masonry-more').masonryMore();


    $('.nav-search-bar').searchBar();
    $('.search-form').submit(function(event) { event.preventDefault(); });

    $('.lo-editable-text').editableText();
    $('.lo-users-finder').usersFinder();
    $(".lo-map-location").gotoMapLocation();

  
    //   $(".hero-image-upload").imageUpload({
    //   multiple:false,
    //   dataType:'Itinerary',
    //   dataID:{{isset($itinerary) ? $itinerary->id:'null'}}, 
    //   dataPath:'assets/content/itinerary',
    //   uploadOnAdd:{{isset($itinerary) ? 'true':'false'}},
    //   property:"heroPhoto",
    //   error: function(e, data) {
    //     $("#form-status").formStatus(data);
    //   },
    // });
    
    $(".lo-image-uploader").imageUpload({
    
    });    


    $.timeago.settings.cutoff = 0;
    $(".timeago").timeago();


    $(document).on('click', '.lo-spot-itinerary', function(event) {
      event.preventDefault();
      if($(this).attr('data-toggle') != 'popover') {
        $(this).spotItinerary().spotItinerary('toggle');
      }
      else {
        console.log('already open...');
        $(this).spotItinerary('toggle');  
      }
    });




    $(document).on('click', '.lo-spot-favorite', function(event) {
      if($(this).data().hasOwnProperty('lo-spotFavorite') == false) {
        $(this).spotFavorite().spotFavorite('click', event);  
      }
    });

    $(".message-modal").each(function(i, e) {
      $(this).modal('show');
    });

    $(document).on('click', '.lo-copy-clipboard', function(e) {
      var client = new ZeroClipboard( $(this) );
    });
    

    $window = $(window);
    $('.hero-image').each(function(){
        var $scroll = $(this);
        $(window).scroll(function() {
          var yPos = 50 + (Math.min($window.scrollTop(), 450)/ 450) * -50.0; 
          var coords = "0px " + yPos + '%';
           $scroll.css({ backgroundPosition:coords });
        });
     });


  }

	return self;
})();




$(document).ready(function($) {
	
  App.init();	

  $(window).resize(function(event) {
    App.windowResize(event);
  });

  App.loadModules();


  $('#user-assets-modal').modal();

  
  
  

  




});

