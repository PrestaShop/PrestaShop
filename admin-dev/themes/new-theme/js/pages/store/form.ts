/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
  ]);

  const $stateSelect = $('[data-country-id]');
  const preselectedStateId = $stateSelect.val() as string;

  if (preselectedStateId && $stateSelect[0]) {
    const observer = new MutationObserver(() => {
      if ($stateSelect.find('option').length > 0) {
        $stateSelect.val(preselectedStateId).trigger('change');
        observer.disconnect();
      }
    });
    observer.observe($stateSelect[0], {childList: true});
  }

  new window.prestashop.component.CountryStateSelectionToggler(
    '[data-states-url]',
    '[data-country-id]',
    '.js-store-state-row',
  );
});
