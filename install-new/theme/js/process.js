var is_installing = false;
$(document).ready(function()
{
	$("#loaderSpace").unbind('ajaxStart');
	start_install();
});

is_installing_count = 0;
is_installing_max = 5;
current_step = 0;
function start_install()
{
	// If we are already installing PrestaShop, do not trigger action again
	if (is_installing)
		return;
	is_installing = true;
	
	$('.process_step').removeClass('fail').removeClass('success').hide();
	$('.error_log').hide();
	setTimeout(text_is_installing, 500);
	$('#progress_bar').show();
	$('#progress_bar .installing').show();
	process_install();
}

function process_install(step)
{
	if (!step)
		step = process_steps[0];
	
	$.ajax({
		url: 'index.php',
		data: step+'=true',
		dataType: 'json',
		cache: false,
		success: function(json)
		{
			// No error during this step
			if (json.success)
			{
				$('#progress_bar_'+step).addClass('complete');
				$('#process_step_'+step).show().addClass('success');
				current_step++;
				if (current_step >= process_steps.length)
				{
					// Installation finished
					setTimeout(function()
					{
						install_success();
					}, 700)
				}
				else
				{
					// Process next step
					process_install(process_steps[current_step]);
				}
			}
			// An error occured during this step
			else
			{
				install_error(step, json.message);
			}
		},
		// An error HTTP (page not found, json not valid, etc.) occured during this step
		error: function()
		{
			install_error(step);
		}
	});
}

function install_error(step, errors)
{
	current_step = 0;
	is_installing = false;
	$('#process_step_'+step).show().addClass('fail');
	$.each(process_steps, function(k, v)
	{
		$('#progress_bar_'+v).removeClass('complete');
	});
	$('#progress_bar .installing').hide();
	
	if (errors)
	{
		var list_errors = errors;
		if ($.type(list_errors) == 'string')
			list_errors = [list_errors];
		
		var display = '<ol>';
		$.each(list_errors, function(k, v)
		{
			display += '<li>'+v+'</li>';
		});
		display += '</ol>';
		$('#process_step_'+step+' .error_log').html(display).show();
	}
}

function install_success()
{
	is_installing = false;
	$('#install_process_form').slideUp();
	$('#install_process_success').slideDown();
}

function text_is_installing()
{
	if (!is_installing)
		return;

	var text = '';
	for (var i = 0; i <= is_installing_count; i++)
		text += '.';
	$('#progress_bar .installing span').html(text);
	
	is_installing_count++;
	if (is_installing_count == is_installing_max)
		is_installing_count = 0;
	
	setTimeout(text_is_installing, 500);
}