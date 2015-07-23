var AnimationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';


(function($) {

	$.fn.extend({		
		addCSSAnimation: function(type, onEnd)
		{
			var anmType = 'animated '+type;
			$(this).addClass(anmType);
			$(this).one(AnimationEnd, function() {
				$(this).removeClass(anmType);
				if(onEnd) {
					onEnd();
				}
			});
		}
	});

})(jQuery);
