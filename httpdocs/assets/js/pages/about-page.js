$(document).ready(function($) {
	$(".about-content-container").height($(document).height());	
});
$(document).resize(function(event) {
	$(".about-content-container").height($(document).height());	
});