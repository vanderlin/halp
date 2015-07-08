(function($) {


    $.widget('lo-mobile.carousel', {
    	options: {
    	},

    	// -------------------------------------
    	_swiper:null,

    	// -------------------------------------
		_create: function() 
		{
			
			var self = this;
		


			this._swiper = new Swiper(this.element, {
				pagination: '.swiper-pagination',
		        paginationClickable: true,
		        initialSlide:this.element.data('initialSlide')||0,
		        autoplay: 2500,
        		autoplayDisableOnInteraction: false
			});
  
    	},    	
    });
}(jQuery));















