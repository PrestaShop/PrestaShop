jQuery(document).ready(function() {
	$('.button.new-item').click(function() {
		var item_container = $(this).parent('.new-item');
		item_container.toggleClass('active').children('.item-container').slideToggle();
	});
	$('.button-edit').click(function() {
		var item_container = $(this).parent('.item');
		item_container.toggleClass('active').children('.item-container').slideToggle();
	});
	$('.button-close').click(function() {
		var item_container = $(this).parent('.item');
		item_container.toggleClass('active').children('.item-container').slideToggle();
	});	
	
	$('.lang-flag').click(function() {
		var lang_id = (this.id).substr(5);
		$('.lang-flag').each(function () {
			$(this).removeClass('active');
		});
		$(this).addClass('active');
		$('.lang-content').each(function () {
			$(this).hide();
		});
		$('#items-'+lang_id).show();
	});	
	$('.new-lang-flag').click(function() {
		var lang_id = (this.id).substr(5);
		$('.new-lang-flag').each(function () {
			$(this).removeClass('active');
		});
		$(this).addClass('active');
		$("#lang-id").val(lang_id)
	});
});