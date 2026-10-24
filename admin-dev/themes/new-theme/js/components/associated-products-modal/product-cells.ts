/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';

export type AssociatedProduct = {
  id: number;
  reference: string;
  name: string;
  active: boolean;
};

const router = new Router();

export function buildLink(text: string, href: string): HTMLAnchorElement {
  const link = document.createElement('a');
  link.href = href;
  link.textContent = text;

  return link;
}

/**
 * Builds a row with the ID, reference, linked name and status dot cells; the tbody carries the status labels.
 */
export function buildProductRow(product: AssociatedProduct, tbody: HTMLElement): HTMLTableRowElement {
  const row = document.createElement('tr');
  row.insertCell().textContent = String(product.id);
  row.insertCell().textContent = product.reference;
  row.insertCell().appendChild(
    buildLink(product.name, router.generate('admin_products_edit', {productId: product.id})),
  );

  const status = document.createElement('span');
  status.classList.add('d-inline-block', 'rounded-circle', product.active ? 'bg-success' : 'bg-secondary');
  status.style.width = '12px';
  status.style.height = '12px';
  status.title = <string>(product.active ? tbody.dataset.enabledLabel : tbody.dataset.disabledLabel);
  const statusCell = row.insertCell();
  statusCell.classList.add('text-center');
  statusCell.appendChild(status);

  return row;
}

/**
 * Shows the loading message only when the answer is slow, so a fast answer doesn't flash it.
 */
export function showLoadingWhenSlow(show: () => void, delay = 300): () => void {
  const timer = window.setTimeout(show, delay);

  return () => window.clearTimeout(timer);
}
