/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PaginationServiceType from '@PSTypes/services';

export default class PaginatedTaxRulesService implements PaginationServiceType {
  private listUrl: string;

  constructor(listUrl: string) {
    this.listUrl = listUrl;
  }

  async fetch(offset: number, limit: number): Promise<FetchResponse> {
    const url = new URL(this.listUrl, window.location.origin);
    url.searchParams.set('limit', String(limit));
    url.searchParams.set('offset', String(offset));

    const response = await window.fetch(url.toString(), {
      headers: {'X-Requested-With': 'XMLHttpRequest'},
    });

    return response.json();
  }
}
