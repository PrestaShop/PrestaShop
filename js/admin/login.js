$(function() {
	// Initialize events
	$("#login_form").validate({
		rules: {
			"email":{
				"email": true,
				"required": true
			},
			"passwd": {
				"required": true
			}
		},
		submitHandler: function(form) {
			doAjaxLogin($('#redirect').val());
		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$("#forgot_password_form").validate({
		rules: {
			"email_forgot": {
				"email": true,
				"required": true
			}
		},
		submitHandler: function(form) {
		  doAjaxForgot();
		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$("#reset_password_form").validate({
		rules: {
			"reset_passwd": {
				"required": true
			},
			"reset_confirm": {
				"required": true
			}
		},
		submitHandler: function(form) {
		  doAjaxReset();
		},
		// override jquery validate plugin defaults for bootstrap 3
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});

	$('.show-forgot-password').on('click',function(e) {
		e.preventDefault();
		displayForgotPassword();
	});

	$('.show-login-form').on('click',function(e) {
		e.preventDefault();
		displayLogin();
	});

	if ($('.front_reset')) $('#reset_passwd').focus();
	else $('#email').focus();

	//Tab-index loop
	$('form').each(function(){
		var list  = $(this).find('*[tabindex]').sort(function(a,b){ return a.tabIndex < b.tabIndex ? -1 : 1; }),
			first = list.first();
		list.last().on('keydown', function(e){
			if( e.keyCode === 9 ) {
				first.focus();
				return false;
			}
		});
	});

	//Preload images
	$('<img/>')[0].src = img_dir+'preston-login@2x.png';
	$('<img/>')[0].src = img_dir+'preston-login-wink@2x.png';

	$('button[name="submitLogin"]').on('mouseover', function() {
		$('#shop-img img').attr('src', img_dir+'preston-login-wink@2x.png');
	});

	$('button[name="submitLogin"]').on('mouseout', function() {
		$('#shop-img img').attr('src', img_dir+'preston-login@2x.png');
	});
});

//todo: ladda init
var l = new Object();
function feedbackSubmit() {
	l = Ladda.create( document.querySelector( 'button[type=submit]' ) );
}

function displayForgotPassword() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.front').hide()},200);
	setTimeout(function(){$('.back').show();$('#email_forgot').select();},200);
	return false;
}

function displayForgotConfirm() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.back').hide()},200);
	setTimeout(function(){$('.forgot_confirm').show()},300);
	return false;
}

function displayResetPassword() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.front').hide()},200);
	setTimeout(function(){$('.front_reset').show();$('#reset_passwd').select();},200);
	return false;
}

function displayResetConfirm() {
	$('#error').hide();
	$('.show-forgot-password').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.front').hide()},200);
	setTimeout(function(){$('.back_reset').show()},200);
	setTimeout(function(){displayLogin()},5000);
	return false;
}

function displayLogin() {
	$('#error').hide();
	$("#login").find('.flip-container').toggleClass("flip");
	setTimeout(function(){$('.back').hide()},200);
	setTimeout(function(){$('.front_login').show();$('#email').select();},200);
	return false;
}

/**
 * Check user credentials
 *
 * @param string redirect name of the controller to redirect to after login (or null)
 */
function doAjaxLogin(redirect) {
	$('#error').hide();
	$('#login_form').fadeIn('slow', function() {
		$.ajax({
			type: "POST",
			headers: { "cache-control": "no-cache" },
			url: "index.php" + '?rand=' + new Date().getTime(),
			async: true,
			dataType: "json",
			data: {
				ajax: "1",
				token: "",
				controller: "AdminLogin",
				submitLogin: "1",
				passwd: $('#passwd').val(),
				email: $('#email').val(),
				redirect: redirect,
				stay_logged_in: $('#stay_logged_in:checked').val()
			},
			beforeSend: function() {
				feedbackSubmit();
				l.start();
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
					l.stop();
				} else {
					window.location.assign(jsonData.redirect);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				l.stop();
				$('#error').html('<h3>TECHNICAL ERROR:</h3><p>Details: Error thrown: ' + XMLHttpRequest + '</p><p>Text status: ' + textStatus + '</p>').removeClass('hide');
				$('#login_form').fadeOut('slow');
			}
		});
	});
}

function doAjaxForgot() {
	$('#error').hide();
	$('#forgot_password_form').fadeIn('slow', function() {
		$.ajax({
			type: 'POST',
			headers: {'cache-control': 'no-cache'},
			url: 'index.php?rand=' + new Date().getTime(),
			async: true,
			dataType: 'json',
			data: {
				ajax: 1,
				controller: 'AdminLogin',
				submitForgot: 1,
				email_forgot: $('#email_forgot').val()
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
				} else {
					$('#forgot_password_form').hide();
					$('.show-forgot-password').hide();
					displayForgotConfirm();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html(XMLHttpRequest.responseText).removeClass('hide').fadeIn('slow');
			}
		});
	});
}

function doAjaxReset() {
	$('#error').hide();
	$('#reset_password_form').fadeIn('slow', function() {
		$.ajax({
			type: 'POST',
			headers: {'cache-control': 'no-cache'},
			url: 'index.php?rand=' + new Date().getTime(),
			async: true,
			dataType: 'json',
			data: {
				ajax: 1,
				controller: 'AdminLogin',
				submitReset: 1,
				reset_token: $('#reset_token').val(),
				id_employee: $('#id_employee').val(),
				reset_email: $('#reset_email').val(),
				reset_passwd: $('#reset_passwd').val(),
				reset_confirm: $('#reset_confirm').val()
			},
			success: function(jsonData) {
				if (jsonData.hasErrors) {
					displayErrors(jsonData.errors);
				} else {
					$('#reset_password_form').hide();
					$('.show-forgot-password').hide();
					displayResetConfirm();
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('#error').html(XMLHttpRequest.responseText).removeClass('hide').fadeIn('slow');
			}
		});
	});
}

function displayErrors(errors) {
	if (errors.length > 1) {
		// If there were multiple issues, we display an error list
		str_errors = '<p><strong>' + more_errors + '</strong></p><ol>';
		for (var error in errors) {
			if (error != 'indexOf') {
				str_errors += '<li>' + errors[error] + '</li>';
			}
		}
		str_errors += '</ol>';
	} else {
		// Otherwise, just the first error in the list
		str_errors = '<p>' + errors[0] + '</p>';
	}
	$('#error').html(str_errors).removeClass('hide').fadeIn('slow');
}
