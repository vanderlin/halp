

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

