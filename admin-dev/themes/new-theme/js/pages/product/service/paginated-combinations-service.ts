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

import Router from '@components/router';
import PaginationServiceType from '@js/types/services';

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
