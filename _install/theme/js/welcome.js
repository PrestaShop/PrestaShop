$(document).ready(function()
{
	// Submit change of language
	$('#langList li input').click(function()
	{
		var form = $('#mainForm');
        form.attr('action', form.attr('action')+'#licenses-agreement');
        form.submit();
	});
	
	// Desactivate next button if licence checkbox is not checked
	if (!$('#set_license').prop('checked'))
	{
		$('#btNext').addClass('disabled').attr('disabled', true);
	}
	
	// Activate / desactivate next button when licence checkbox is clicked
	$('#set_license').click(function()
	{
		if ($(this).prop('checked'))
			$('#btNext').removeClass('disabled').attr('disabled', false);
		else
			$('#btNext').addClass('disabled').attr('disabled', true);
	});
});