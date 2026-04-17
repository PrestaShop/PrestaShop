/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import CategoryTreeFilter from '@pages/product/category/category-tree-filter';
import ProductMap from '@pages/product/product-map';
import selectShopForEdition from '@pages/product/shop/select-shop-modal';
import initGridShopPreviews from '@pages/product/grid/grid-shop-previews';

const {$} = window;

$(() => {
  const grid = new window.prestashop.component.Grid('product');

  grid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.AjaxBulkActionExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
  grid.addExtension(new window.prestashop.component.GridExtensions.PositionExtension(grid));

  grid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension((button: HTMLElement) => {
    if (button.classList.contains(ProductMap.shops.editProductClass)) {
      const shopIds: string[] = button.closest('tr')?.querySelector<HTMLElement>(ProductMap.shops.shopListCell)
        ?.dataset?.shopIds?.split(',') ?? [];
      selectShopForEdition(button, shopIds);
    } else {
      document.location.href = <string> button.getAttribute('href');
    }
  }));

  document.querySelectorAll<HTMLElement>(`.${ProductMap.shops.editProductClass}`).forEach((link: HTMLElement) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      if (link.classList.contains(ProductMap.shops.editProductClass)) {
        const shopIds: string[] = link.closest('tr')?.querySelector<HTMLElement>(ProductMap.shops.shopListCell)
          ?.dataset?.shopIds?.split(',') ?? [];
        selectShopForEdition(link, shopIds);
      } else {
        document.location.href = <string> link.getAttribute('href');
      }
    });
  });
  initGridShopPreviews();

  new CategoryTreeFilter();
});
