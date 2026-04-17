/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CustomerThreadViewPageMap from './customer-thread-view-page-map';

const {$} = window;

$(() => {
  $(CustomerThreadViewPageMap.forwardEmployeeInput).on('change', (event) => {
    const $someoneElseEmailInput = $(
      CustomerThreadViewPageMap.forwardSomeoneElseEmailInput,
    );
    const $someElseEmailFormGroup = $someoneElseEmailInput.closest(
      '.form-group',
    );

    const employeeId = $(event.currentTarget).val();

    if (parseInt(<string>employeeId, 10) === 0) {
      $someElseEmailFormGroup.removeClass('d-none');
    } else {
      $someElseEmailFormGroup.addClass('d-none');
    }
  });
});
