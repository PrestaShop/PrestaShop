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
import EntitySearchInput, {EntitySearchInputOptions} from '@components/entity-search-input';

type Product = {
  name: string,
  reference: string,
  image: string,
}

export default class ProductSearchInput extends EntitySearchInput {
  constructor(
    searchInputContainer: string,
    options: OptionsObject = <EntitySearchInputOptions> {},
  ) {
    const searchInputContainerEl = <HTMLElement> document.querySelector(searchInputContainer);
    const referenceLabel = searchInputContainerEl.dataset.referenceLabel ?? '(Ref: %s)';

    // eslint-disable-next-line no-param-reassign
    options.suggestionTemplate = (product: Product) => {
      let reference = '';

      if (product.reference) {
        reference = `<span class="product-reference">(${product.reference})</span>`;
      }

      return `<div class="search-suggestion"><img src="${product.image}" /> ${product.name}${reference}</div>`;
    };
    // eslint-disable-next-line no-param-reassign
    options.responseTransformer = (response: any) => {
      Object.keys(response).forEach((key) => {
        if (Object.prototype.hasOwnProperty.call(response, key)) {
          const combination = response[key];

          if (combination.reference) {
            // eslint-disable-next-line no-param-reassign
            response[key].reference = referenceLabel.replace('%s', combination.reference);
          }
        }
      });

      return response;
    };

    super($(searchInputContainer), options);
  }
}
