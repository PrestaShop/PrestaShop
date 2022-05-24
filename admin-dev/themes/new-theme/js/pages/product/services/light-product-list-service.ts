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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import Router from '@components/router';
import PaginationServiceType from '@PSTypes/services';

const {$} = window;

//@todo: work in progress. Lots of code still unused.
export default class LightProductListService implements PaginationServiceType {
  productId: number;

  router: Router;

  filters: Record<string, any>;

  orderBy: string | null;

  orderWay: string | null;

  constructor(productId: number) {
    this.productId = productId;
    this.router = new Router();
    this.filters = {};
    this.orderBy = null;
    this.orderWay = null;
  }

  fetch(offset: number, limit: number): JQuery.jqXHR<any> {
    const filterId = 'light_product';
    const requestParams: Record<string, any> = {};

    // These are the query parameters
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

    return $.get(this.router.generate('admin_products_v2_get_light_list', requestParams));
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
}
