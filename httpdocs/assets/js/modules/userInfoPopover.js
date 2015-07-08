
// jQuery.fn.extend({
// 	userInfoPopover: function() {
//     console.log('this');
// 		this.popover({
//     		trigger:'manual',
//     		content:'<ul class="list-unstyled popover-list user-image-name">\
//               		 	<li><a href="'+$(this).attr('href')+'">'+$(this).data('name')+'</a><div class="location"><small class="text-muted">'+$(this).data('location')+'</small></div></li>\
//             		 </ul>',
//       	})
//       	.on("mouseenter", function() {
//         	var _this = this;
//         	$(this).popover("show");
//         	var $popover = $('#'+$(this).attr('aria-describedby'));
//         	$popover.on("mouseleave", function() {
//           	$(_this).popover('hide');
//         });
//       	}).on("mouseleave", function() {
//         	var _this = this;
//         	setTimeout(function() {
//           		if (!$(".popover:hover").length) {
//             		$(_this).popover("hide")
//           		}
//         	}, 100);
//     });	
// 	},
// });

