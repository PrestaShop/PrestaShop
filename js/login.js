$(document).ready(function() {
	// Focus on email address field
	$('#email').select();

	// Initialize events
	$('#login_form').submit(function(e) {
		// Kill default behaviour
		e.preventDefault();
		doAjaxLogin($('#redirect').val());
	});

	$('#forgot_password_form').submit(function(e) {
		// Kill default behaviour
		e.preventDefault();
		doAjaxForgot();
	});

	$('.show-forgot-password').click(function(e) {
		// Kill default behaviour
		e.preventDefault();
		displayForgotPassword();
	});

	$('.show-login-form').click(function(e) {
		// Kill default behaviour
		e.preventDefault();
		displayLogin();
	});
});


function displayForgotPassword() {
	$('#error').hide();
	$('#login_form').fadeOut('fast', function () {
		$("#forgot_password_form").fadeIn('fast');
		// Focus on email address forgot field
		$('#email_forgot').select();
	});

}

function displayLogin() {
	$('#error').hide();

	$('#forgot_password_form').fadeOut('fast', function () {
		$('#login_form').fadeIn('fast');
		// Focus on email address field
		$('#email').select();
	});

	return false;
}

/**
 * Check user credentials
 *
 * @param string redirect name of the controller to redirect to after login (or null)
 */
function doAjaxLogin(redirect) {
	$('#error').hide();
	$('#login_form .ajax-loader').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			headers: { "cache-control": "no-cache" },
			url: "ajax-tab.php" + '?rand=' + new Date().getTime(),
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
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>').show();
				$('#login_form .ajax-loader').fadeOut('slow');
			}
		});
	});
}
function doAjaxForgot() {
	$('#error').hide();
	$('#forgot_password_form .ajax-loader').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			headers: { "cache-control": "no-cache" },
			url: "ajax-tab.php" + '?rand=' + new Date().getTime(),
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
				if (jsonData.hasErrors)
					displayErrors(jsonData.errors);
				else
				{
					alert(jsonData.confirm);
					$('#forgot_password_form .ajax-loader').hide();
					displayLogin();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>').show();
				$('#forgot_password_form .ajax-loader').fadeOut('slow');
			}
		});
	});
}
function displayErrors(errors) {
	str_errors = '<h3>' + (errors.length > 1 ? there_are : there_is) + ' ' + errors.length + ' ' + (errors.length > 1 ? label_errors : label_error) + '</h3><ol>';
	for (var error in errors) //IE6 bug fix
		if (error != 'indexOf') str_errors += '<li>' + errors[error] + '</li>';
	$('.ajax-loader').hide();
	$('#error').html(str_errors + '</ol>').fadeIn('slow');
	$("#login").effect("shake", {
		times: 4
	}, 100);
}
