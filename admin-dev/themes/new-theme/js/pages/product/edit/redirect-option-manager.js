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

import Bloodhound from "typeahead.js";
import ProductSearchInput from "@pages/product/edit/product-search-input";

const {$} = window;

export default class RedirectOptionManager {
  constructor($productSearchInput) {
    this.$productSearchInput = $productSearchInput;
    this.$autoCompleteSearchContainer = this.$productSearchInput.closest('.autocomplete-search');
    this.buildAutoCompleteSearchInput();
  }

  buildAutoCompleteSearchInput() {
    const sourceConfig = {
      mappingValue: this.$autoCompleteSearchContainer.data('mappingvalue'),
      remoteUrl: this.$productSearchInput.data('productSearchUrl'),
    };
    const productRemoteSource = this.buildRemoteSource(sourceConfig);
    this.productSearchInput = new ProductSearchInput(this.$productSearchInput, productRemoteSource);
  }

  buildRemoteSource(config) {
    return new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      identify(obj) {
        return obj[config.mappingValue];
      },
      remote: {
        url: config.remoteUrl,
        cache: false,
        wildcard: '%QUERY',
        transform(response) {
          if (!response) {
            return [];
          }
          return response;
        },
      },
    });
  }
}
