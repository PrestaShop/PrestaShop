$(function() {
	$('input[@type=radio].star').rating();
	$('.auto-submit-star').rating();

	$('.open-comment-form').fancybox({
		'hideOnContentClick': false
	});

	$('button.usefulness_btn').click(function() {
		var id_product_comment = $(this).data('id-product-comment');
		var is_usefull = $(this).data('is-usefull');
		var parent = $(this).parent();

		$.ajax({
			url: productcomments_controller_url,
			data: {
				id_product_comment: id_product_comment,
				action: 'comment_is_usefull',
				value: is_usefull
			},
			type: 'POST',
			success: function(result){
				parent.fadeOut('slow', function() {
					parent.remove();
				});
			}
		});
	});

	$('span.report_btn').click(function() {
		if (confirm(confirm_report_message))
		{
			var idProductComment = $(this).data('id-product-comment');
			var parent = $(this).parent();

			$.ajax({
				url: productcomments_controller_url,
				data: {
					id_product_comment: idProductComment,
					action: 'report_abuse'
				},
				type: 'POST',
				success: function(result){
					parent.fadeOut('slow', function() {
						parent.remove();
					});
				}
			});
		}
	});

	$('#submitNewMessage').click(function(e) {
		// Kill default behaviour
		e.preventDefault();

		// Form element

		url_options = parseInt(productcomments_url_rewrite) ? '?' : '&';
		$.ajax({
			url: productcomments_controller_url+url_options+'action=add_comment&secure_key='+secure_key,
			data: $('#fancybox-content form').serialize(),
			type: 'POST',
			dataType: "json",
			success: function(data){
				if (data.result)
				{
					$.fancybox.close();
					document.location.href = document.location.href;
				}
				else
				{
					$('#new_comment_form_error ul').html('');
					$.each(data.errors, function(index, value) {
						$('#new_comment_form_error ul').append('<li>'+value+'</li>');
					});
					$('#new_comment_form_error').slideDown('slow');
				}
			}
		});
		return false;
	});
});
