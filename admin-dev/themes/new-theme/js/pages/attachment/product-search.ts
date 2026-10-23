/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {
  AssociatedProduct, buildLink, buildProductRow, showLoadingWhenSlow,
} from '@components/associated-products-modal/product-cells';

const {$} = window;

type ProductWithAttachments = AssociatedProduct & {
  attachments: {id: number, name: string, editUrl: string}[];
};

/**
 * Searches the products by ID, reference or name and shows, in a modal, the files each one is associated with.
 */
export default class ProductSearch {
  private readonly $modal: JQuery;

  private readonly tbody: HTMLElement;

  private request: AbortController | null = null;

  constructor() {
    this.$modal = $('#product-search-modal');
    this.tbody = <HTMLElement> this.$modal.find('tbody').get(0);

    $(document).on('submit', '.js-product-search', (event: JQuery.SubmitEvent) => {
      event.preventDefault();
      const form = event.currentTarget as HTMLFormElement;
      const url = new URL(form.action, window.location.href);
      new FormData(form).forEach((value, key) => url.searchParams.set(key, <string>value));
      this.search(url.toString());
    });
  }

  private async search(url: string): Promise<void> {
    this.showState('none');
    const cancelLoading = showLoadingWhenSlow(() => this.showState('loading'));
    this.$modal.modal('show');

    // A new search must not let the previous, slower answer fill the list
    this.request?.abort();
    const request = new AbortController();
    this.request = request;

    try {
      const response = await fetch(url, {signal: request.signal});

      if (!response.ok) {
        throw new Error(response.statusText);
      }
      const data: {products: ProductWithAttachments[], limited: boolean} = await response.json();
      this.render(data.products);
      this.$modal.find('.js-product-search-limited').toggleClass('d-none', !data.limited);
      this.showState(data.products.length > 0 ? 'results' : 'empty');
    } catch (error) {
      if (!request.signal.aborted) {
        this.showState('error');
      }
    } finally {
      cancelLoading();
    }
  }

  private render(products: ProductWithAttachments[]): void {
    const rows = document.createDocumentFragment();
    products.forEach((product) => {
      const row = buildProductRow(product, this.tbody);
      const attachmentsCell = row.insertCell();
      product.attachments.forEach((attachment) => {
        const line = document.createElement('div');
        line.appendChild(buildLink(attachment.name, attachment.editUrl));
        attachmentsCell.appendChild(line);
      });
      rows.appendChild(row);
    });
    this.tbody.replaceChildren(rows);
  }

  private showState(state: 'none' | 'loading' | 'results' | 'empty' | 'error'): void {
    this.$modal.find('.js-product-search-loading').toggleClass('d-none', state !== 'loading');
    this.$modal.find('.js-product-search-results').toggleClass('d-none', state !== 'results');
    this.$modal.find('.js-product-search-empty').toggleClass('d-none', state !== 'empty');
    this.$modal.find('.js-product-search-error').toggleClass('d-none', state !== 'error');
  }
}
