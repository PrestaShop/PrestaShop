/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(function()
{
	// Desactivate next button if licence checkbox is not checked
	if (!$('#set_license').prop('checked'))
	{
		$('#btNext').addClass('disabled').attr('disabled', true);
	}

	// Activate / desactivate next button when licence checkbox is clicked
	$('#set_license').on('click', function()
	{
		if ($(this).prop('checked'))
			$('#btNext').removeClass('disabled').attr('disabled', false);
		else
			$('#btNext').addClass('disabled').attr('disabled', true);
	});

	if ($('#set_license').prop('checked'))
		$('#btNext').removeClass('disabled').attr('disabled', false);
	else
		$('#btNext').addClass('disabled').attr('disabled', true);
});
