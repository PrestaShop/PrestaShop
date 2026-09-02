/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import LocalizationPageMap from '@pages/localization/LocalizationPageMap';

$(() => {
  // Show warning message when currency is changed
  $(LocalizationPageMap.formDefaultCurrency).on('change', function () {
    alert($(this).data('warning-message'));
  });
});
