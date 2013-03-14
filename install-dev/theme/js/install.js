$(document).ready(function()
{
	$('#mainForm').submit(function()
	{
		$('#btNext').hide();
	});

	// Ajax animation
	$("#loaderSpace").ajaxStart(function()
	{
		$(this).fadeIn('slow');
		$(this).children('div').fadeIn('slow');
	});

	$("#loaderSpace").ajaxComplete(function(e, xhr, settings)
	{
		$(this).fadeOut('slow');
		$(this).children('div').fadeOut('slow');
	});

	$('select.chosen').chosen();
	
	// try to pre-compile the smarty templates	
	function compile_smarty_templates(bo)
	{
		$.ajax(
		{
			url: 'index.php',
			data: {
				'compile_templates': 1,
				'bo':bo
			},
			global: false
		});
	}
	compile_smarty_templates(1);
	compile_smarty_templates(0);
});
