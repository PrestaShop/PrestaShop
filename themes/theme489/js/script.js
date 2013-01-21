
$(function(){
$('#setCurrency').jqTransform({imgPath:'jqtransformplugin/img/'});
});

/*
$(document).ready(function()
{
$('.mnf_sup_list li .right_side p a').each(function() {
var h = $(this).html();
var index = h.indexOf(' ');
if(index == -1) {
index = h.length;
}
$(this).html('<span class="firstWord">' + h.substring(0, index) + '</span>' + h.substring(index, h.length));
});
}); 
*/








	jQuery(document).ready(function(){
		jQuery('#tmspecials').css({visibility:'visible',display:'block'});
	// hide #back-top first
	jQuery("#back-top").hide();
	// fade in #back-top
	jQuery(function () {
		jQuery(window).scroll(function () {
			if (jQuery(this).scrollTop() > 100) {
				jQuery('#back-top').fadeIn();
			} else {
				jQuery('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		jQuery('#back-top a').click(function () {
			jQuery('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

});