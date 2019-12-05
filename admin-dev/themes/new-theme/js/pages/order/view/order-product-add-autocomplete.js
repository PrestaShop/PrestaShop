/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Router from '../../../components/router';
import OrderViewPageMap from "../OrderViewPageMap";

const $ = window.$;

export default class OrderProductAutocomplete {
  constructor(input) {
    this.router = new Router();
    this.input = input;
    this.results = {};
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
    this.input.on('keyup', event => this.search(event.target.value));
    $(document).on('click', () => this.dropdownMenu.hide());
  }

  search(search) {
    $.ajax(this.router.generate('admin_products_search', {search_phrase: search}), {
      method: 'GET',
    }).then(results => this.updateResults(results));
  }

  updateResults(results) {
    this.results = results;
    this.dropdownMenu.empty();
    Object.entries(this.results).forEach((val) => {
      const link = $(`<a class="dropdown-item" data-id="${val[1].productId}" href="#">${val[1].name}</a>`);
      link.on('click', event => this.onItemClicked($(event.target).data('id')));
      this.dropdownMenu.append(link);
    });
    this.dropdownMenu.toggle(Object.keys(this.results).length > 0);
  }

  onItemClicked(id) {
    this.input.val(this.results[id].name);
    this.onItemClickedCallback(this.results[id]);
  }
}
