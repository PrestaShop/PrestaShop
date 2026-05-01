/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
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

  const grid = new window.prestashop.component.Grid('store');

  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
