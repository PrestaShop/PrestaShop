/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import PaginationServiceType from '@PSTypes/services';

const {$} = window;

export default class PaginatedCatalogPriceRulesService implements PaginationServiceType {
  productId: number;

  router: Router;

  offset: number;

  limit: number;

  constructor(productId: number) {
    this.productId = productId;
    this.router = new Router();
    this.offset = 0;
    this.limit = 0;
  }

  fetch(offset: number, limit: number): JQuery.jqXHR<any> {
    return $.get(this.router.generate('admin_catalog_price_rules_list_for_product', {
      productId: this.productId,
      limit,
      offset,
    }));
  }
}
