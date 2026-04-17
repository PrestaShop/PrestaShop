/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TaxMap from '@pages/tax/tax-map';
import DisplayInCartOptionHandler from '@pages/tax/display-in-cart-option-handler';

const {$} = window;

$(() => {
  const taxGrid = new window.prestashop.component.Grid('tax');

  taxGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  taxGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  new DisplayInCartOptionHandler();

  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
      'TranslatableInput',
    ],
  );

  $(TaxMap.optionsForm.useEcoTax).on('change', (event) => {
    const useEcoTax = Number($(event.currentTarget).val());
    $(TaxMap.optionsForm.rowEcoTaxRuleGroup).toggleClass('d-none', useEcoTax === 0);
  });
});
