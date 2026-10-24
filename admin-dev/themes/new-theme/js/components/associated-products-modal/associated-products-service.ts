/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PaginationServiceType from '@PSTypes/services';
import {AssociatedProduct} from './product-cells';

export type AssociatedProductsPage = {
  products: AssociatedProduct[];
  total: number;
  failed?: boolean;
};

/**
 * Fetches the pages of the opened row's products, filtered by the search phrase. Answers are kept for the page, so
 * going back to a page or opening a row again shows it at once.
 */
export default class AssociatedProductsService implements PaginationServiceType {
  private listUrl = '';

  private searchPhrase = '';

  private lastRequest = '';

  private readonly pages = new Map<string, Promise<AssociatedProductsPage>>();

  setListUrl(listUrl: string): void {
    this.listUrl = listUrl;
  }

  setSearchPhrase(searchPhrase: string): void {
    this.searchPhrase = searchPhrase.trim();
  }

  prefetch(listUrl: string, limit: number): void {
    this.load(this.buildUrl(listUrl, 0, limit, ''));
  }

  async fetch(offset: number, limit: number): Promise<FetchResponse> {
    const url = this.buildUrl(this.listUrl, offset, limit, this.searchPhrase);
    this.lastRequest = url;

    let page: AssociatedProductsPage;
    try {
      page = await this.load(url);
    } catch (error) {
      page = {products: [], total: 0, failed: true};
    }

    // A newer request (another row, page or search) replaces this one, which must not be shown
    if (this.lastRequest !== url) {
      return new Promise(() => {
        // Never settles, so the paginator doesn't render it
      });
    }

    return page;
  }

  private load(url: string): Promise<AssociatedProductsPage> {
    let page = this.pages.get(url);

    if (!page) {
      page = window.fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
        .then((response) => {
          if (!response.ok) {
            throw new Error(response.statusText);
          }

          return response.json();
        });
      // A failed request is retried on the next opening
      page.catch(() => this.pages.delete(url));
      this.pages.set(url, page);
    }

    return page;
  }

  private buildUrl(listUrl: string, offset: number, limit: number, searchPhrase: string): string {
    const url = new URL(listUrl, window.location.origin);
    url.searchParams.set('limit', String(limit));
    url.searchParams.set('offset', String(offset));

    if (searchPhrase !== '') {
      url.searchParams.set('phrase', searchPhrase);
    }

    return url.toString();
  }
}
