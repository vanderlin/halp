/**
 * jQuery Plugin: Sticky Tabs
 *
 * @author Aidan Lister <aidan@php.net>
 * @version 1.2.0
 */
(function ( $ ) {
    $.fn.stickyTabs = function( options ) {
        var context = this

        var settings = $.extend({
            getHashCallback: function(hash, btn) { return hash },
            selectorAttribute: "href"
        }, options );

        // Show the tab corresponding with the hash in the URL, or the first tab.
        var showTabFromHash = function() {
          var hash = settings.selectorAttribute == "href" ? window.location.hash : window.location.hash.substring(1); //Omit the hash character ('#');
          var selector = hash ? 'a[' + settings.selectorAttribute +'="' + hash + '"]' : 'li.active > a';
          $(selector, context).tab('show');
        }

        // We use pushState if it's available so the page won't jump, otherwise a shim.
        var changeHash = function(hash) {
          if (history && history.pushState) {
            history.pushState(null, null, '#' + hash);
          } else {
            scrollV = document.body.scrollTop;
            scrollH = document.body.scrollLeft;
            window.location.hash = hash;
            document.body.scrollTop = scrollV;
            document.body.scrollLeft = scrollH;
          }
        }

        // Set the correct tab when the page loads
        showTabFromHash(context)

        // Set the correct tab when a user uses their back/forward button
        $(window).on('hashchange', showTabFromHash);

        // Change the URL when tabs are clicked
        $('a', context).on('click', function(e) {
          var hash = this.href.split('#')[1];
          var adjustedhash = settings.getHashCallback(hash, this);
          changeHash(adjustedhash);
        });

        return this;
    };
}( jQuery ));


$.fn.getRotationDegrees = function() {
    var obj = $(this);
    var matrix = obj.css("-webkit-transform") ||
    obj.css("-moz-transform")    ||
    obj.css("-ms-transform")     ||
    obj.css("-o-transform")      ||
    obj.css("transform");
    if(matrix !== 'none') {
        var values = matrix.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180/Math.PI));
    } else { var angle = 0; }
    return (angle < 0) ? angle +=360 : angle;
}


$.fn.animateRotate = function(angle, duration, easing, complete) {
  return this.each(function() {
    var $elem = $(this);
    var currentAngle = $(this).getRotationDegrees();

    $({deg: currentAngle}).animate({deg: angle}, {
      duration: duration,
      easing: easing,
      step: function(now) {
        $elem.css({
           transform: 'rotate(' + now + 'deg)'
         });
      },
      complete: complete || $.noop
    });
  });
};


jQuery(document).ready(function($) {  
var originalLeave = $.fn.popover.Constructor.prototype.leave;
$.fn.popover.Constructor.prototype.leave = function(obj){
  var self = obj instanceof this.constructor ?
    obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
  var container, timeout;

  originalLeave.call(this, obj);

  if(obj.currentTarget) {
    container = $(obj.currentTarget).siblings('.popover')
    timeout = self.timeout;
    container.one('mouseenter', function(){
      //We entered the actual popover – call off the dogs
      clearTimeout(timeout);
      //Let's monitor popover content instead
      container.one('mouseleave', function(){
        $.fn.popover.Constructor.prototype.leave.call(self, self);
      });
    })
  }
};
});








