/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import previewProductsToggler from '@pages/order/preview-products-toggler';

const {$} = window;

$(() => {
  const orderGrid = new window.prestashop.component.Grid('order');
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.ModalFormSubmitExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.ChoiceExtension());
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.PreviewExtension(previewProductsToggler, orderGrid));
  orderGrid.addExtension(new window.prestashop.component.GridExtensions.BulkOpenTabsExtension());
});
