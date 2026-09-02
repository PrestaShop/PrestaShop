/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * /!\ This file is deprecated, it will be deleted in next major release /!\
 */

$(function() {
	if ($('input[name=PS_MAIL_METHOD]:checked').val() == 2)
		$('#mail_fieldset_smtp').show();
	else
		$('#mail_fieldset_smtp').hide();

	$('input[name=PS_MAIL_METHOD]').on('click', function() {
		if ($(this).val() == 2)
			$('#mail_fieldset_smtp').slideDown();
		else
			$('#mail_fieldset_smtp').slideUp();
	});
});
