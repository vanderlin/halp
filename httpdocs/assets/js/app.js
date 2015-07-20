function prevent_default(e) {
    e.preventDefault();
}
function disable_scroll() {
    $(document).on('touchmove', prevent_default);
}
function enable_scroll() {
    $(document).unbind('touchmove', prevent_default)
}

function getURLParameters(paramName)
{
    var sURL = window.document.URL.toString();
    if (sURL.indexOf("?") > 0)
    {
        var arrParams = sURL.split("?");
        var arrURLParams = arrParams[1].split("&");
        var arrParamNames = new Array(arrURLParams.length);
        var arrParamValues = new Array(arrURLParams.length);

        var i = 0;
        for (i = 0; i<arrURLParams.length; i++)
        {
            var sParam =  arrURLParams[i].split("=");
            arrParamNames[i] = sParam[0];
            if (sParam[1] != "")
                arrParamValues[i] = unescape(sParam[1]);
            else
                arrParamValues[i] = "No Value";
        }

        for (i=0; i<arrURLParams.length; i++)
        {
            if (arrParamNames[i] == paramName)
            {
                //alert("Parameter:" + arrParamValues[i]);
                return arrParamValues[i];
            }
        }
        return null;
    }
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

        $(document).on('click', '.halp-edit-button', function(e) {
            // alert("Still working on this...");
            e.preventDefault();
            var $card = $('.task-card-'+$(this).data('id'));
                $card.addClass('edit-card');
            var self = this;    
            var $taskdetails = $card.find('.task-details');
            $taskdetails.fadeToggle(200, function() {
                $(this).addClass('edit');   
                
                var $title = $card.find('.task-name');
                var $input = $('\<div class="task-edit">\
                    <input autocomplete="off" type="text" name="title" value="'+$title.data('value')+'">\
                    <input autocomplete="off" type="text" name="duration" value="'+$title.data('value')+'">\
                    <input autocomplete="off" type="text" name="project" value="'+$title.data('value')+'">\
                    <a href="#cancel" class="cancel-edit">Cancel</a>\
                    <div>').insertAfter($taskdetails);

                // change edit button
                $(self).find('span').html('Save');
                        
                $input.hide();
                $input.fadeIn(200);

            });
            
        });

        $(document).on('click', '.close-popup', function(e) {
            App.closeClaimPopup();
        });

        $(document).on('click', '#claim-task-form button', function(e) {
            e.preventDefault();
            var isEmail = $(this).attr('id')==='email-task';
            
            var $form = $('#claim-task-form'); 
            var fd = new FormData($form[0]);               
            $.ajax({
                url: $form.attr('action'),
                data: fd,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
            })
            .always(function(e) {
                console.log(e);
                if(e.status == 200)
                {
                    
                    
                    $('.task-card-'+e.task.id).hide();

                    $('.white-popup .task-message p').fadeTo(200, 0);
                    $('.white-popup .claimed-buttons').fadeTo(200, 0, function() {
                        $('#claim-task-form button').prop( "disabled", true );
                    });
                    $('.white-popup h2').fadeTo(100, 0, function() {
                        $(this).html('Thanks for helping!').fadeTo(100, 1, function() {
                            $('.front-facing-turtle').animate({'margin-bottom':-80})
                            setTimeout(function() {
                                App.closeClaimPopup();
                                window.location = window.location.origin;
                            }, 500);
                        });
                    });
                }
                else {
                    console.log("Some error during task claim");
                }
            });    
        })
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

    // -------------------------------------
    self.addDeleteTaskEvent = function($item) {
        $item.click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var c = confirm("Are you sure you want to delete this task?");
            var $target = $($(this).data('target'));
            if(c)
            {
                $.ajax({
                    url: '/tasks/'+id,
                    type: 'POST',
                    dataType:'json',
                    data: {_method: 'DELETE'},
                })
                .done(function(e) {
                    $target.fadeOut(200, function() {
                        $(this).remove();
                    });
                });
                 
            } 
        });
    }
    
    // -------------------------------------
    self.closeClaimPopup = function(id) 
    {
        $('.white-popup').fadeOut(200, function() {
            $.magnificPopup.close();    
        })
    }

    // -------------------------------------
    self.openClaimPopup = function(id) 
    {
        $.magnificPopup.open({
            tLoading: 'Loading some halp!...',
            closeOnContentClick: false,
            closeOnBgClick:false,
            mainClass: 'mfp-fade',
            items: {
                src: '/tasks/'+id+'/claimed',
                type: 'ajax',
            },
            callbacks: {
                parseAjax: function(mfpResponse) {
                    var task = mfpResponse.xhr.responseText;
                    mfpResponse.data = $(mfpResponse.xhr.responseText);
                },
                ajaxContentAdded: function() {
                    console.log(this.content);                   
                }
            }
        });
    }

    // -------------------------------------
    self.loadModules = function() {

        var self = this;

        $.timeago.settings.cutoff = 0;
        $(".timeago").timeago();
        

        $('.halp-delete-task-button').each(function(index, el) {
            self.addDeleteTaskEvent($(el));
        });

        // -------------------------------------
        $('.halp-claim-button').each(function(index, el) {
            $(el).click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                App.openClaimPopup(id);
            });
        });
    
        


    }

	return self;
})();



// ------------------------------------------------------------------------
$(document).ready(function($) {
  App.init();	
  $(window).resize(function(event) {
    App.windowResize(event);
  });
  App.loadModules();
});
// ------------------------------------------------------------------------
