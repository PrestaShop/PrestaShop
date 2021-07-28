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
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

const {$} = window;

export default class OrderProductAutocomplete {
  constructor(input) {
    this.activeSearchRequest = null;
    this.router = new Router();
    this.input = input;
    this.results = [];
    this.dropdownMenu = $(OrderViewPageMap.productSearchInputAutocompleteMenu);
    /**
     * Permit to link to each value of dropdown a callback after item is clicked
     */
    this.onItemClickedCallback = () => {};
  }

  listenForSearch() {
    this.input.on('click', (event) => {
      event.stopImmediatePropagation();
      this.updateResults(this.results);
    });

    this.input.on('keyup', (event) => this.delaySearch(event.currentTarget));
    $(document).on('click', () => this.dropdownMenu.hide());
  }

  delaySearch(input) {
    clearTimeout(this.searchTimeoutId);

    // Search only if the search phrase length is greater than 2 characters
    if (input.value.length < 2) {
      return;
    }

    this.searchTimeoutId = setTimeout(() => {
      this.search(input.value, $(input).data('currency'), $(input).data('order'));
    }, 300);
  }

  search(search, currency, orderId) {
    const params = {search_phrase: search};

    if (currency) {
      params.currency_id = currency;
    }

    if (orderId) {
      params.order_id = orderId;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    this.activeSearchRequest = $.get(this.router.generate('admin_orders_products_search', params));
    this.activeSearchRequest
      .then((response) => this.updateResults(response))
      .always(() => {
        this.activeSearchRequest = null;
      });
  }

  updateResults(results) {
    this.dropdownMenu.empty();

    if (!results || !results.products || Object.keys(results.products).length <= 0) {
      this.dropdownMenu.hide();
      return;
    }

    this.results = results.products;

    Object.values(this.results).forEach((val) => {
      const link = $(`<a class="dropdown-item" data-id="${val.productId}" href="#">${val.name}</a>`);

      link.on('click', (event) => {
        event.preventDefault();
        this.onItemClicked($(event.target).data('id'));
      });

      this.dropdownMenu.append(link);
    });

    this.dropdownMenu.show();
  }

  onItemClicked(id) {
    const selectedProduct = this.results.filter((product) => product.productId === id);

    if (selectedProduct.length !== 0) {
      this.input.val(selectedProduct[0].name);
      this.onItemClickedCallback(selectedProduct[0]);
    }
  }
}
