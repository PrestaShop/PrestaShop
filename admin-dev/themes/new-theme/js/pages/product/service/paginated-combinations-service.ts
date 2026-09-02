/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';
import PaginationServiceType from '@PSTypes/services';

const {$} = window;

export default class PaginatedCombinationsService implements PaginationServiceType {
  productId: number;

  shopId: number;

  router: Router;

  filters: Record<string, any>;

  offset: number;

  limit: number;

  orderBy: string | null;

  orderWay: string | null;

  constructor(productId: number, shopId: number) {
    this.productId = productId;
    this.shopId = shopId;
    this.router = new Router();
    this.filters = {};
    this.offset = 0;
    this.limit = 0;
    this.orderBy = null;
    this.orderWay = null;
  }

  fetch(offset: number, limit: number): JQuery.jqXHR<any> {
    this.offset = offset;
    this.limit = limit;

    const filterId = this.getFilterId();
    const requestParams: Record<string, any> = {};
    // Required for route generation
    requestParams.productId = this.productId;

    // These are the query parameters
    requestParams.shopId = this.shopId;
    requestParams[filterId] = {};
    requestParams[filterId].offset = offset;
    requestParams[filterId].limit = limit;
    requestParams[filterId].filters = this.filters;
    if (this.orderBy !== null) {
      requestParams[filterId].orderBy = this.orderBy;
    }
    if (this.orderWay !== null) {
      requestParams[filterId].sortOrder = this.orderWay;
    }

    return $.get(this.router.generate('admin_products_combinations', requestParams));
  }

  getCombinationIds(): JQuery.jqXHR<any> {
    return $.get(
      this.router.generate('admin_products_combinations_ids', {
        productId: this.productId,
        shopId: this.shopId,
      }), {
        [this.getFilterId()]: {
          filters: this.filters,
          // It is important that we reset offset and limit, because we want to get all results without pagination
          offset: null,
          limit: null,
        },
      },
    );
  }

  setOrderBy(orderBy: string, orderWay: string): void {
    this.orderBy = orderBy;
    this.orderWay = orderWay.toLowerCase() === 'desc' ? 'DESC' : 'ASC';
  }

  getFilters(): Record<string, any> {
    return this.filters;
  }

  setFilters(filters: Record<string, any>): void {
    this.filters = filters;
  }

  private getFilterId(): string {
    return `product_combinations_${this.productId}`;
  }
}
