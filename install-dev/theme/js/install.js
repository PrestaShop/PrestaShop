/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(function()
{
	$('#mainForm').on('submit', function() {
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

	$('select.chosen').not('.no-chosen').chosen();
});
