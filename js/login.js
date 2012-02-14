$(document).ready(function() {
	if (document.getElementById('email')) document.getElementById('email').focus(); //$("#login").effect( "slide", { direction: "up" }, 1000 );
});

function displayForgotPassword() {
	$('#error').hide();
	$("#login").flip({
		direction: 'tb',
		color: '#FFF',
		content: $('#forgot_password')
	})
}
function displayLogin() {
	$('#error').hide();
	$('#login').revertFlip();
	return false;
}

/**
 * Check user credentials
 *
 * @param string redirect name of the controller to redirect to after login (or null)
 */
function doAjaxLogin(redirect) {
	$('#error').hide();
	$('#ajax-loader').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			url: "ajax-tab.php",
			async: true,
			dataType: "json",
			data: {
				ajax: "1",
				token: "",
				controller: "AdminLogin",
				submitLogin: "1",
				passwd: $('#passwd').val(),
				email: $('#email').val(),
				redirect: redirect
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
				} else {
					window.location.href = jsonData.redirect;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>');
				$('#error').fadeIn();
				$('#ajax-loader').fadeOut('slow');
			}
		});
	});
}
function doAjaxForgot() {
	$('#error').hide();
	$('#ajax-loader').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			url: "ajax-tab.php",
			async: true,
			dataType: "json",
			data: {
				ajax: "1",
				token: "",
				controller: "AdminLogin",
				submitForgot: "1",
				email_forgot: $('#email_forgot').val()
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
				} else {
					window.location.href = jsonData.redirect;
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>');
				$('#error').fadeIn();
				$('#ajax-loader').fadeOut('slow');
			}
		});
	});
}
function displayErrors(errors) {
	str_errors = '<h3>' + (errors.length > 1 ? there_are : there_is) + ' ' + errors.length + ' ' + (errors.length > 1 ? label_errors : label_error) + '</h3><ol>';
	for (error in errors) //IE6 bug fix
	if (error != 'indexOf') str_errors += '<li>' + errors[error] + '</li>';
	$('#ajax-loader').fadeOut('slow');
	$('#error').html(str_errors + '</ol>');
	$('#error').fadeIn();
	$("#login").effect("shake", {
		times: 4
	}, 100);
}
