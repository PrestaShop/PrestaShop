/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {AssociatedProduct, buildProductRow, showLoadingWhenSlow} from './product-cells';

const {$} = window;

/**
 * Lists the products of a grid row (an attachment, a category) in a modal opened from its preview button, with a filter
 * on the ID, reference or name. The list is requested as soon as the pointer reaches the button and kept for the page.
 */
export default class AssociatedProductsModal {
  private readonly $modal: JQuery;

  private readonly tbody: HTMLTableSectionElement;

  private readonly filterInput: HTMLInputElement;

  private readonly products = new Map<string, Promise<AssociatedProduct[]>>();

  private openedUrl: string | null = null;

  constructor() {
    this.$modal = $('#associated-products-modal');
    this.tbody = <HTMLTableSectionElement> this.$modal.find('tbody').get(0);
    this.filterInput = <HTMLInputElement> this.$modal.find('.js-associated-products-filter').get(0);

    $(document)
      .on('mouseenter focusin', '.js-associated-products-preview', (event: JQuery.TriggeredEvent) => {
        this.load(<string>(event.currentTarget as HTMLElement).dataset.url);
      })
      .on('click', '.js-associated-products-preview', (event: JQuery.ClickEvent) => {
        const button = event.currentTarget as HTMLElement;
        this.open(<string>button.dataset.url, <string>button.dataset.title);
      });
    this.filterInput.addEventListener('input', () => this.filter());
    this.$modal.on('shown.bs.modal', () => this.filterInput.focus());
  }

  private load(url: string): Promise<AssociatedProduct[]> {
    let products = this.products.get(url);

    if (!products) {
      products = fetch(url)
        .then((response) => {
          if (!response.ok) {
            throw new Error(response.statusText);
          }

          return response.json();
        })
        .then((data: {products: AssociatedProduct[]}) => data.products);
      // A failed request is retried on the next opening
      products.catch(() => this.products.delete(url));
      this.products.set(url, products);
    }

    return products;
  }

  private async open(url: string, title: string): Promise<void> {
    this.openedUrl = url;
    this.$modal.find('.modal-title').text(title);
    this.filterInput.value = '';
    this.showState('none');
    const cancelLoading = showLoadingWhenSlow(() => this.showState('loading'));
    this.$modal.modal('show');

    try {
      const products = await this.load(url);

      // Another row may have been opened while this list was loading
      if (this.openedUrl === url) {
        this.render(products);
        this.showState('table');
      }
    } catch (error) {
      if (this.openedUrl === url) {
        this.showState('error');
      }
    } finally {
      cancelLoading();
    }
  }

  private render(products: AssociatedProduct[]): void {
    const rows = document.createDocumentFragment();
    products.forEach((product) => {
      const row = buildProductRow(product, this.tbody);
      row.dataset.id = String(product.id);
      row.dataset.text = `${product.reference}\n${product.name}`.toLowerCase();
      rows.appendChild(row);
    });
    this.tbody.replaceChildren(rows);
    this.filter();
  }

  private filter(): void {
    const phrase = this.filterInput.value.trim().toLowerCase();
    let visibleRows = 0;

    Array.from(this.tbody.rows).forEach((row) => {
      // Same rule as the search by product: the ID must match exactly
      const matches = row.dataset.id === phrase || (<string>row.dataset.text).includes(phrase);
      row.toggleAttribute('hidden', !matches);
      visibleRows += matches ? 1 : 0;
    });
    this.$modal.find('.js-associated-products-no-match').toggleClass('d-none', visibleRows > 0);
  }

  private showState(state: 'none' | 'loading' | 'table' | 'error'): void {
    this.$modal.find('.js-associated-products-loading').toggleClass('d-none', state !== 'loading');
    this.$modal.find('.js-associated-products-table').toggleClass('d-none', state !== 'table');
    this.$modal.find('.js-associated-products-error').toggleClass('d-none', state !== 'error');
  }
}
