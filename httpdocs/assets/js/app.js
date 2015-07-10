function prevent_default(e) {
    e.preventDefault();
}
function disable_scroll() {
    $(document).on('touchmove', prevent_default);
}
function enable_scroll() {
    $(document).unbind('touchmove', prevent_default)
}
// ------------------------------------------------------------------------

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




    // ------------------------------------------------------------------------
		self.init = function() {
      
      console.log("App Init");
      
      // init window resize 
      self.windowResize();

      // scroll
      // ------------------------------
			$('.scroll-to-anchor').each(function(i, e) {
				var target = $(e).attr('data-scroll-to');
				$(e).click(function(event) {
					self.scrollTo(target);
				});
			});


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
  
    }


    self.loadModules = function() {
        $.timeago.settings.cutoff = 0;
        $(".timeago").timeago();
      
        $('.halp-claim-button').each(function(index, el) {
            var $button = $(el);
            var id = $button.data('id');
            
            $button.magnificPopup({
                items: {
                    src: '/tasks/'+id+'/claimed',
                    type: 'ajax'
                },
                callbacks: {
                    parseAjax: function(mfpResponse) {
                    // mfpResponse.data is a "data" object from ajax "success" callback
                    // for simple HTML file, it will be just String
                    // You may modify it to change contents of the popup
                    // For example, to show just #some-element:
                    // mfpResponse.data = $(mfpResponse.data).find('#some-element');
                    //console.log($(mfpResponse.data));
                    // mfpResponse.data must be a String or a DOM (jQuery) element
                    var task = mfpResponse.xhr.responseText;
                  
                    mfpResponse.data = $(mfpResponse.xhr.responseText);
                  },
                  ajaxContentAdded: function() {
                    // Ajax content is loaded and appended to DOM
                    console.log(this.content);
                   
                  }
                }
            });
            $button.on('mfpOpen', function(e /*, params */) {
                console.log('Popup opened',  $.magnificPopup.instance);
            });

            if(index == 4) {
                $button.magnificPopup('open'); 
            }
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
});

