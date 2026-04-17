/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import DiscountMap from '@pages/discount/discount-map';

$(() => {
  const discountGrid = new window.prestashop.component.Grid('discount');

  discountGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  discountGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  new window.prestashop.component.GridExtensions.FilterLinkGroup();

  // Check the type selected and update the submit button state
  const discountTypeRadios = document.querySelectorAll<HTMLInputElement>(DiscountMap.discountTypeRadios);
  discountTypeRadios.forEach((discountTypeRadio: HTMLInputElement) => {
    discountTypeRadio.addEventListener('change', () => {
      const discountTypeSubmit = document.querySelector<HTMLInputElement>(DiscountMap.discountTypeSubmit);

      if (discountTypeSubmit) {
        discountTypeSubmit.disabled = discountTypeRadio.value === '';
        discountTypeSubmit.classList.toggle('disabled', discountTypeRadio.value === '');
      }
    });
  });
});
