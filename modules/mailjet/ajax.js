// JavaScript Document
$(document).ready(function() {

	$('#button_test_mailjet').click(function() {
		$("#mailjet_test_ok").hide();
		$("#mailjet_test_ko").hide();
		$("#div_email_test").show(500);
	});

	$('#button_send_mailjet').click(function() {
		var token = $(this).attr('rel');
		$("#button_test_mailjet").hide();
		$("#image_ajax_mailjet").show();
		$("#div_email_test").hide();
		$.ajax({
			type: 'GET',
			url: "../modules/mailjet/ajax.php",
			async: true,
			cache: false,
			dataType : "html",
			data: 'token=' + token + '&mailjet_api_key=' + $("#mailjet_api_key").val() + '&mailjet_secret_key=' + $("#mailjet_secret_key").val() + '&email_from=' + escape($("#email_from").val()),
			success: function(html)
			{
				$("#button_test_mailjet").show();
				$("#image_ajax_mailjet").hide();
				var retour = html.split("|");
			
				if (retour[0] == "true")
				{
					$("#mailjet_test_ok").show(500);
				} else {
					$("#mailjet_activation_no").attr('checked', true);
					$("#mailjet_error_message").html(retour[1]);
					$("#mailjet_test_ko").show(500);
				
				}
			},
			error: function(jqxhr, status, errorThrown)
			{
				$("#mailjet_activation_no").attr('checked', true);
				$("#mailjet_error_message").html(errorThrown);
				$("#mailjet_test_ko").show(500);
			}
		});
	});

});
