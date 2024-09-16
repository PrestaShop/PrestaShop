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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
