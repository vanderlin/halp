function prevent_default(e) {
    e.preventDefault();
}
function disable_scroll() {
    $(document).on('touchmove', prevent_default);
}
function enable_scroll() {
    $(document).unbind('touchmove', prevent_default)
}

function getQueryParams(queryString) {
  var query = (queryString || window.location.search).substring(1); // delete ?
  if (!query) {
    return false;
  }
  return _
  .chain(query.split('&'))
  .map(function(params) {
    var p = params.split('=');
    return [p[0], decodeURIComponent(p[1])];
  })
  .object()
  .value();
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

        // -------------------------------------
        $(document).on('click', '.halp-edit-button', function(e) {
            var id = $(this).data('id');
            console.log("Edit button task", id);
            App.editTask(id);   
        });
    
        // -------------------------------------
        $(document).on('click', '.close-popup', function(e) {
            App.closeClaimPopup();
        });
    
        
        $('input[name="project"]').autocomplete({
            source: projects,
            minLength: 0
        })
        .focus(function() {
            $(this).autocomplete('search', $(this).val())
        });

        // -------------------------------------
        $(document).on('click', '#claim-task-form button', function(e) {
            e.preventDefault();
            var isEmail = $(this).attr('id')==='email-task';
            
            var $form = $('#claim-task-form'); 
            var fd = new FormData($form[0]);               
            $.ajax({
                url: $form.attr('action')+'?view=true',
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

                    $(this).popupResponse(e, {
                        height:500,
                        delay:3500,
                        callback:function() {
                            var $card = $('.task-card-'+e.task.id);
                            App.scrollTo($('#claimed-tasks-content'), 500, function() {
                                $card.remove();
                                var $view = $(e.view);
                                $('#claimed-tasks-content').prepend($view);
                                $view.hide();
                                $view.addClass('task-focused');
                                $view.delay(200).fadeTo(500, 1);
                            });
                        }
                        
                    })
                }
                else {
                    console.log("Some error during task claim");
                }
            });    
        })
	}
	
    // ------------------------------------------------------------------------
    self.editTask = function(id)
    {
        var self = this;
        $.magnificPopup.open({
            tLoading: 'Loading your task!...',
            closeOnContentClick: false,
            closeOnBgClick:false,
            mainClass: 'mfp-fade',
            items: {
                src: '/tasks/edit/'+id,
                type: 'ajax',
            },
            callbacks: {
                parseAjax: function(mfpResponse) {
                    var task = mfpResponse.xhr.responseText;
                    mfpResponse.data = $(mfpResponse.xhr.responseText);
                },
                ajaxContentAdded: function() {
                    // console.log(this.content[2]); 
                    var $content = $(this.content[2]);
                    
                    $("#edit-task-datepicker").datepicker({showAnim:'slideDown'});
                    $("#edit-task-datepicker").datepicker("setDate", new Date( $("#edit-task-datepicker").data('default-date') )); 
                    $("#edit-task-title").on('keydown, keyup', function(event) {
                        var v = $(this).val();
                        if(v=="") {
                            v = 'Untitled';
                        }
                        $('.edit-task-content h2 .task-title').html(v.trim());
                    });
                       
                    
                    $('#edit-task-project').autocomplete({
                        source: projects,
                        appendTo:$('#edit-task-project').parent(),
                        minLength: 0
                    })
                    .focus(function() {
                        $(this).autocomplete('search', $(this).val())
                    });

                    // -------------------------------------
                    $('#edit-task-form').submit(function(e) {
                        e.preventDefault();
                        var validation = $(this).validateTask();
                        if(validation.valid) 
                        {
                            var $form = $(this);
                            var url = $form.prop('action')+'?view=true';
                            var type = $form.attr('method');
                            var fd = new FormData($form[0]);    
                            $.ajax({
                                url: url,
                                type: type,
                                dataType: 'json',
                                data: fd,
                                processData: false,
                                contentType: false,
                            })
                            .always(function(e) {
                                console.log(e);
                                $(this).popupResponse(e, {
                                    callback: function() {
                                        var $card = $('.task-card-'+e.task.id);
                                        App.scrollTo($card, 500, function() {
                                            $card.fadeTo(200, 0, function() {
                                                var $view = $(e.view);
                                                $(this).replaceWith($view);
                                                $view.hide();
                                                $view.addClass('task-focused');
                                                $view.delay(200).fadeTo(200, 1);
                                            });
                                        });
                                    }
                                })
                            });
                        }
                        else
                        {
                            console.log("Invalid Edit Form");
                        }    
                    });
                
                    $('#edit-task-form').addValidationListener();

                }
            }
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
    self.closePopup = function(callback) 
    {
        
        $('.white-popup').fadeOut(200, function() {
            $.magnificPopup.close();    
            if(callback) {
                callback();
            }   
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
