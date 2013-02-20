$(document).ready(function()
{
	// Submit change of language
	$('#langList li input').click(function()
	{
		var form = $('#mainForm');
        form.attr('action', form.attr('action')+'#licenses-agreement');
        form.submit();
	});
});