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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import ProductMap from '@pages/product/product-map';

export default function initGridShopPreviews(): void {
  document.querySelectorAll<HTMLElement>(ProductMap.shops.shopPreviews.toggleButtons).forEach((toggleButton) => {
    toggleButton.addEventListener('click', () => {
      const row = toggleButton.closest<'tr'>('tr');

      if (!row || row.classList.contains(ProductMap.shops.shopPreviews.loadingRowClass)) {
        return;
      }

      if (!row.classList.contains(ProductMap.shops.shopPreviews.expandedShopRowClass)) {
        expandRow(row, toggleButton);
      } else {
        collapseRow(row, toggleButton);
      }
    });
  });
}

async function expandRow(row:HTMLTableRowElement, toggleButton: HTMLElement): Promise<void> {
  if (!toggleButton.dataset.shopPreviewsUrl || !toggleButton.dataset.productId) {
    return;
  }

  // Start loading
  row.classList.add(ProductMap.shops.shopPreviews.loadingRowClass);
  const detailsResponse = await fetch(toggleButton.dataset.shopPreviewsUrl);

  if (!detailsResponse.ok) {
    return;
  }

  row.classList.remove(ProductMap.shops.shopPreviews.loadingRowClass);
  row.classList.add(ProductMap.shops.shopPreviews.expandedShopRowClass);
  row.setAttribute('data-product-id', toggleButton.dataset.productId);

  const body = await detailsResponse.text();
  row.insertAdjacentHTML('afterend', body);
}

function collapseRow(row:HTMLTableRowElement, toggleButton: HTMLElement): void {
  row.classList.remove(ProductMap.shops.shopPreviews.expandedShopRowClass);
  document.querySelectorAll<HTMLTableRowElement>(
    ProductMap.shops.shopPreviews.productPreviewsSelector(<string> toggleButton.dataset.productId),
  ).forEach((shopPreviewRow: HTMLTableRowElement): void => {
    shopPreviewRow.remove();
  });
}
