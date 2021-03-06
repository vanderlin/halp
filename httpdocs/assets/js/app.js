
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
      case "true": case "yes": case "1": case 1: return true;
      case "false": case "no": case "0": case null: case 0: return false;
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
            e.preventDefault();
            var id = $(this).data('id');
            console.log("Edit button task", id);
            App.editTask(id);   
        });

    
        // -------------------------------------
        $(document).on('click', '.halp-claim-button', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            console.log("Claim button task", id);
            App.openPopup({
                url:'/tasks/'+id+'/claimed'
            });
        });
    

        // -------------------------------------
        $(document).on('click', '.halp-delete-task-button', function(e) {
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
    
        // -------------------------------------
        $(document).on('click', '.close-popup', function(e) {
            App.closePopup();
        });
        
        // -------------------------------------
        $(".return-task form").submit(function(e) {
            e.preventDefault();
            var c = confirm("Are you sure you want to return this task?");
            if(c)
            {
                var $form = $(this); 
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
                    var $card = $('.task-card-'+e.task.id);
                        $card.fadeTo(200, 0, function() {
                            $(this).remove();
                        })       
                });
            }
        });
        // -------------------------------------
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
                        close:false,
                        callback:function(e) {
                            $('#claimed-close-popup-button').click(function(evt) {
                                evt.preventDefault();
                                App.closePopup(function() {
                                    var $card = $('.task-card-'+e.task.id);
                                    App.scrollTo($('#claimed-tasks-content'), 500, function() {
                                        $card.remove();
                                        var $view = $(e.view);
                                        $('#claimed-tasks-content').prepend($view);
                                        $view.hide();
                                        $view.addClass('task-focused');
                                        $view.delay(200).fadeTo(500, 1);
                                    });
                                })
                                
                            });
                            
                        }
                        
                    })
                }
                else {
                    console.log("Some error during task claim");
                }
            });    
        })
    
        // -------------------------------------
        $(document).on('mouseover mouseout', '.front-facing-turtle', function(e) {
            if(e.type == 'mouseover')
            {
                $('.front-facing-turtle').show().animate({top: -10}, {
                    duration: 100, 
                    easing: 'easeOutCubic', 
                });
            }
            else {
                $('.front-facing-turtle').show().animate({top: -44}, {
                    duration: 100, 
                    easing: 'easeOutCubic', 
                });   
            }
        });
        
	}
	
    // ------------------------------------------------------------------------
    self.editTask = function(id)
    {
        this.openPopup({
            loadingMessage: 'Loading your task!...',
            url:'/tasks/edit/'+id,
            onContentAdded: function(e) {
                var $content = $(e.content[2]);
                
                // $('#task-time').timeEntry({show24Hours: true});
                var $datepicker = $("#task-datepicker");
                if($datepicker.data('use-js-picker') == true) {
                    // date picker
                    $datepicker.datepicker({
                        showAnim:'slideDown',
                        minDate:0,
                        onSelect: function(dateText) {
                            $('#date-none-button').parent().removeClass('active');
                            $('input[name="does_not_expire"]').val(false);
                            $(this).addClass('active');
                        }
                    })
                   
                    $datepicker.datepicker("setDate", new Date( $("#edit-task-datepicker").data('default-date') )); 
                }
                    

                $("#edit-task-title").on('keydown, keyup', function(event) {
                    var v = $(this).val();
                    if(v=="") {
                        v = 'Untitled';
                    }
                    $('.edit-task-content h2 .task-title').html(v.trim());
                });
                   
                $('#date-none-button').click(function(e) {
                    e.preventDefault();
                    var $expireInput = $('input[name="does_not_expire"]');
                    var val = !Utils.stringToBool($expireInput.val());
                    $expireInput.val(val);
                    if(val) {
                        $datepicker.removeClass('active');
                        $(this).parent().addClass('active');
                    }
                    else {
                        $(this).parent().removeClass('active');
                    }
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
        });       
    }

    // ------------------------------------------------------------------------
	self.scrollTo = function(target, speed, callback) 
    {
    	var $target = $(target);
        if($target.length) {
            $('body').animate({ scrollTop: $target.offset().top - 100 }, speed||500, function(e) {
                if(callback) {
                    callback(e)
                }
            });
        }
	}

    // ------------------------------------------------------------------------
    self.windowResize = function(e) 
    {
        if($('.front-facing-turtle').length)
        {
            $('.front-facing-turtle').css({left:($('.white-popup').offset().left) + (($('.white-popup').outerWidth()-$('.front-facing-turtle').width())/2)});    
        }
    }

    // -------------------------------------
    self.closePopup = function(callback) 
    {

        
        $('.white-popup').removeClass('animated fadeIn');
        $('.front-facing-turtle').show().animate({top: 20}, {
            duration: 200, 
            easing: 'easeOutCubic', 
            complete:function() {
                $('.ui-datepicker').remove();
                $(this).remove();
                $('.white-popup').fadeOut(400, function() {
                    $.magnificPopup.close();    
                    if(callback) {
                        callback();
                    }   
                })     
            }
        });
    }

    // -------------------------------------
    self.openPopup = function(options) 
    {

        $.magnificPopup.open({
            tLoading: options.loadingMessage||'Loading some halp!...',
            closeOnContentClick: false,
            closeOnBgClick:false,
            mainClass: 'mfp-fade',
            ajax: {
                settings: {
                    data:options.data?options.data:null,
                }
            },
            items: {
                src: options.url,
                type: 'ajax',
            },
            callbacks: {
                afterClose: function() {
                    console.log("afterClose");
                    $('.ui-datepicker').remove();                
                },
                parseAjax: function(mfpResponse) {
                    var task = mfpResponse.xhr.responseText;
                    mfpResponse.data = $(mfpResponse.xhr.responseText);
                },
                ajaxContentAdded: function() {
                    $('.front-facing-turtle').css({left:($('.white-popup').offset().left) + (($('.white-popup').outerWidth()-$('.front-facing-turtle').width())/2)});
                    setTimeout(function() {
                        $('.front-facing-turtle').show().animate({top: -44}, {duration: 200, easing: 'easeOutCubic'});
                    }, 1000);
                    if(options.onContentAdded) {
                        options.onContentAdded(this);
                    }
                }
            }
        });
    }

    // -------------------------------------
    self.loadModules = function() 
    {

        var self = this;

        $.timeago.settings.cutoff = 0;
        $(".timeago").timeago();
        
        // -------------------------------------
        $('#feedback-button').click(function(e) {
            e.preventDefault();
            App.openPopup({
                url:'/feedback'
            });
        });        
        // $('.halp-delete-task-button').each(function(index, el) {
        //     self.addDeleteTaskEvent($(el));
        // });

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
