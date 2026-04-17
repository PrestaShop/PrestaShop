/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ConfirmModal from '@components/modal/confirm-modal';
import PerformancePreferencesPageMap from '@pages/performance-preferences/PerformancePreferencesPageMap';

const {$} = window;

$(() => {
  const $submitBtn = $(PerformancePreferencesPageMap.disableNonBuiltInModulesBtn);
  $submitBtn.on('click', (event: JQueryEventObject) => {
    event.preventDefault();

    const modal = new ConfirmModal(
      {
        confirmTitle: $submitBtn.data('confirmTitle'),
        confirmMessage: '',
        confirmButtonLabel: $submitBtn.data('confirmButtonLabel'),
        closeButtonLabel: $submitBtn.data('closeButtonLabel'),
      },
      () => {
        window.location.href = <string> $submitBtn.attr('href');
      },
    );

    modal.show();
  });

  // Initialize cookie name and value generatable inputs
  window.prestashop.component.initComponents([
    'GeneratableInput',
  ]);
  window.prestashop.instance.generatableInput.attachOn('.js-generator-btn');
});
