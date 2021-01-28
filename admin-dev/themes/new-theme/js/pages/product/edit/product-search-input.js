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

import AutoCompleteSearch from "@components/auto-complete-search";

export default class ProductSearchInput {
  constructor($productSearchInput, productRemoteSource) {
    this.$productSearchInput = $productSearchInput;
    this.productSearchInputId = this.$productSearchInput.prop('id');
    this.productRemoteSource = productRemoteSource;

    this.buildAutoCompleteSearch();
  }

  buildAutoCompleteSearch() {
    this.autoSearch = new AutoCompleteSearch(this.$productSearchInput, {}, {
      source: this.productRemoteSource,
      dataLimit: 1,
      value: (product) => {
        let value = product.id;
        if (Object.prototype.hasOwnProperty.call(product, 'id_product_attribute') && product.id_product_attribute) {
          value = `${value},${product.id_product_attribute}`;
        }

        return value;
      },
      templates: {
        renderSelected: (product) => this.renderSelected(product),
      }
    });
  }

  renderSelected(product) {
    const $templateContainer = $(`#tplcollection-${this.productSearchInputId}`);
    const innerTemplateHtml = $templateContainer
      .html()
      .replace('%s', product.name);

    return `<li class="media">
        <div class="media-left">
          <img class="media-object image" src="${product.image}" />
        </div>
        <div class="media-body media-middle">
          ${innerTemplateHtml}
        </div>
      </li>`;
  }
}
