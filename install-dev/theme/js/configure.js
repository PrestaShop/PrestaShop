/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

$(document).ready(function() {
  checkTimeZone($('#infosCountry'));
  // When a country is changed
  $('#infosCountry').change(function()
	{
	  checkTimeZone(this);
  });

  watchPasswordStrength($('#infosPassword'));
});

function checkTimeZone(elt)
{
  var iso = $(elt).val();

  // Get timezone by iso
  $.ajax({
	url: 'index.php',
	data: 'timezoneByIso=true&iso='+iso,
	dataType: 'json',
	cache: true,
	success: function(json) {
	  if (json.success) {
		$('#infosTimezone').val(json.message).trigger("liszt:updated");
		if (in_array(iso, ['br','us','ca','ru','me','au','id']))
		{
		  if ($('#infosTimezone:visible').length == 0 && $('#infosTimezone_chosen').length == 0)
		  {
			$('#infosTimezone:hidden').show();
			$('#timezone_div').show();
			$('#infosTimezone').chosen();
		  }
		  $('#timezone_div').show();
		}
		else
		  $('#timezone_div').hide();
	  }
	}
  });
}

function in_array(needle, haystack) {
  var length = haystack.length;
  for (var i = 0; i < length; i++) {
    if (haystack[i] == needle)
	  return true;
  }
  return false;
}

/**
 * Watch password, which is entered in the input, strength and inform about it.
 *
 * @param {jQuery} element the input to watch.
 */
function watchPasswordStrength(element) {
  element.on('keyup', function checkPasswordStrength() {
    const passwordValue = $(this).val();
    const popoverElement = $('.field-password .popover');
    let $feedbackContainer = $(this).parent().find('.password-strength-feedback');

    if ($feedbackContainer.length === 0) {
      $(this).parent().append($('#password-feedback').html());
      $feedbackContainer = $(this).parent().find('.password-strength-feedback');
    }

    const passwordRequirementsLength = $feedbackContainer.find('.password-requirements-length');
    passwordRequirementsLength.find('span').text(
      sprintf(
        passwordRequirementsLength.data('translation'),
        $(this).data('minlength'),
        $(this).data('maxlength'),
      ),
    );

    const passwordRequirementsScore = $feedbackContainer.find('.password-requirements-score');
    passwordRequirementsScore.find('span').text(
      sprintf(
        passwordRequirementsScore.data('translation'),
        $feedbackContainer.data('translations')[$(this).data('minscore')],
      ),
    );

    if (passwordValue === '') {
      $feedbackContainer.toggleClass('d-none', true);
      popoverElement.toggleClass('d-none', true);
    } else {
      const result = zxcvbn(passwordValue);
      displayFeedback($(this), $feedbackContainer, result);
      $feedbackContainer.removeClass('d-none');
    }
  });
}

/**
 * Display feedback about password's strength.
 *
 * @param {jQuery} $passwordInput The currenct password field
 * @param {jQuery} $outputContainer a container to put feedback output into.
 * @param {ZXCVBNResult} result
 *
 * @private
 */
function displayFeedback(
  $passwordInput,
  $outputContainer,
  result,
) {
  const feedback = this.getPasswordStrengthFeedback(result.score);
  const translations = $outputContainer.data('translations');
  const popoverContent = [];
  const popoverElement = $('.field-password .popover');
  const popoverBody = $('.popover-body', popoverElement);

  $outputContainer.find('.password-strength-text').text(translations[result.score]);

  if (result.feedback.warning !== '') {
    if (result.feedback.warning in translations) {
      popoverContent.push(translations[result.feedback.warning]);
    }
  }

  result.feedback.suggestions.forEach((suggestion) => {
    if (suggestion in translations) {
      popoverContent.push(translations[suggestion]);
    }
  });

  popoverBody.html(popoverContent.join('<br>'));

  const passwordLength = $passwordInput.val().length;

  popoverElement.toggleClass('d-none', popoverContent.length <= 0);

  const passwordLengthValid = passwordLength >= $passwordInput.data('minlength')
    && passwordLength <= $passwordInput.data('maxlength');
  $outputContainer.find('.password-requirements-length svg').toggleClass(
    'text-success',
    passwordLengthValid,
  );

  const passwordScoreValid = $passwordInput.data('minscore') <= result.score;
  $outputContainer.find('.password-requirements-score svg').toggleClass(
    'text-success',
    passwordScoreValid,
  );

  $passwordInput
    .removeClass()
    .addClass(passwordScoreValid && passwordLengthValid ? 'border-success' : 'border-danger')
    .addClass('form-control border');

  // Calculate the pourcentage of the bar, depending on the score.
  const percentage = (result.score * 20) + 20;

  // increase and decrease progress bar
  $outputContainer
    .find('.progress-bar')
    .width(`${percentage}%`)
    .css('visibility', 'visible')
    .css('background-color', feedback.color);
}

/**
 * Get feedback that describes given password strength.
 * Response contains text message and element class.
 *
 * @param {number} strength
 *
 * @private
 */
function getPasswordStrengthFeedback(
  strength
) {
  switch (strength) {
  case 0:
    return {
      color: '#D5343C',
    };

  case 1:
    return {
      color: '#D5343C',
    };

  case 2:
    return {
      color: '#FFA000',
    };

  case 3:
    return {
      color: '#21834D',
    };

  case 4:
    return {
      color: '#21834D',
    };

  default:
    throw new Error('Invalid password strength indicator.');
  }
}
