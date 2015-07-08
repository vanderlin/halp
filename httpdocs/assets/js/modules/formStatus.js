jQuery.fn.extend({
  formStatus: function(e, options) {
    
    options = options || {};
    var self = $(this)
    var res = e.responseJSON || e;
    
    function showMessage(message, type, fadeOut) {
        
        fadeOut = fadeOut===false ? false : true;
        console.log(fadeOut);
        var $element = $('<div class="alert '+type+'">'+message+'</div>');
        self.append($element);
        if(fadeOut) {
            $element.fadeOut(0).fadeIn(300).delay(2000).fadeOut(300, function() {
                $(this).remove();
                if(options.onDone) {
                    options.onDone(this);
                }
            });
        }
        else {
            $element.fadeOut(0).fadeIn(300);
        }
    }

    
    if(typeof res == 'object') {
    	console.log(res.errors);
    	if(res.errors && typeof res.errors == 'object' && res.errors.length > 0) {
    		for (var i = 0; i < res.errors.length; i++) {

    			showMessage(res.errors[i], 'alert-error alert-danger', options.fadeOut);
    		}
    	}
        else if(res.notice != undefined) {
            showMessage(res.notice, 'alert bg-success', options.fadeOut);
        }
    }

    	/*reach ($errors as $err)
	        <div class="alert alert-error alert-danger">
                @if (is_array($err))
                    {{$err[0]}}
                @else
                    {{$err}}
                @endif
            </div>*/
  },
});
