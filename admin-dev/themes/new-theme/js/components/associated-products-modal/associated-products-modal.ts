/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import DynamicPaginator from '@components/pagination/dynamic-paginator';
import RendererType from '@PSTypes/renderers';
import AssociatedProductsService, {AssociatedProductsPage} from './associated-products-service';
import {buildProductRow, showLoadingWhenSlow} from './product-cells';

const {$} = window;

const SEARCH_DELAY = 300;

/**
 * Lists the products of a grid row (an attachment, a category) page by page in a modal opened from its preview
 * button, with a search on the ID, reference or name. The first page is requested as soon as the pointer reaches
 * the button.
 */
export default class AssociatedProductsModal implements RendererType {
  private readonly $modal: JQuery;

  private readonly tbody: HTMLTableSectionElement;

  private readonly searchInput: HTMLInputElement;

  private readonly limitSelect: HTMLSelectElement;

  private readonly service = new AssociatedProductsService();

  private readonly paginator: DynamicPaginator;

  private cancelLoading?: () => void;

  private searchTimer?: number;

  constructor() {
    this.$modal = $('#associated-products-modal');
    this.tbody = <HTMLTableSectionElement> this.$modal.find('tbody').get(0);
    this.searchInput = <HTMLInputElement> this.$modal.find('.js-associated-products-search').get(0);
    this.limitSelect = <HTMLSelectElement> this.$modal.find('#paginator-limit').get(0);
    this.paginator = new DynamicPaginator('#associated-products-pagination', this.service, this);

    $(document)
      .on('mouseenter focusin', '.js-associated-products-preview', (event: JQuery.TriggeredEvent) => {
        this.service.prefetch(<string>(event.currentTarget as HTMLElement).dataset.url, Number(this.limitSelect.value));
      })
      .on('click', '.js-associated-products-preview', (event: JQuery.ClickEvent) => {
        const button = event.currentTarget as HTMLElement;
        this.open(<string>button.dataset.url, <string>button.dataset.title);
      });
    this.searchInput.addEventListener('input', () => {
      window.clearTimeout(this.searchTimer);
      this.searchTimer = window.setTimeout(() => this.search(), SEARCH_DELAY);
    });
    this.$modal.on('shown.bs.modal', () => this.searchInput.focus());
  }

  setLoading(loading: boolean): void {
    this.cancelLoading?.();

    if (loading) {
      this.cancelLoading = showLoadingWhenSlow(() => this.showState('loading'));
    }
  }

  render(data: Record<string, unknown>): void {
    const page = <AssociatedProductsPage> data;

    if (page.failed) {
      this.showState('error');

      return;
    }

    const rows = document.createDocumentFragment();
    page.products.forEach((product) => rows.appendChild(buildProductRow(product, this.tbody)));
    this.tbody.replaceChildren(rows);
    this.$modal.find('.js-associated-products-no-match').toggleClass('d-none', page.products.length > 0);
    this.showState('table');
  }

  private open(url: string, title: string): void {
    window.clearTimeout(this.searchTimer);
    this.$modal.find('.modal-title').text(title);
    this.searchInput.value = '';
    this.service.setListUrl(url);
    this.service.setSearchPhrase('');
    this.showState('none');
    this.$modal.modal('show');
    this.paginator.paginate(1);
  }

  private search(): void {
    this.service.setSearchPhrase(this.searchInput.value);
    this.paginator.paginate(1);
  }

  private showState(state: 'none' | 'loading' | 'table' | 'error'): void {
    this.$modal.find('.js-associated-products-loading').toggleClass('d-none', state !== 'loading');
    this.$modal.find('.js-associated-products-table').toggleClass('d-none', state !== 'table');
    this.$modal.find('.js-associated-products-error').toggleClass('d-none', state !== 'error');
  }
}
