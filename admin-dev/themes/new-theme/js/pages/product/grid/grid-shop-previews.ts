/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
