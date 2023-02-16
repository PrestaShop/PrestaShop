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
