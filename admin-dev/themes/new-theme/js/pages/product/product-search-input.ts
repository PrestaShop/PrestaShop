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
import EntitySearchInput from '@components/entity-search-input';
import ProductEventMap from '@pages/product/product-event-map';
import EventEmitter from '@components/event-emitter';

export type Product = {
  product_id: number,
  unique_identifier: string
  name: string,
  reference: string,
  combination_id: number,
  image: string,
  quantity: number
}

export default class ProductSearchInput extends EntitySearchInput {
  constructor(
    searchInputContainer: string,
    referenceLabel: string = '(Ref: %s)',
  ) {
    const eventEmitter = <typeof EventEmitter> window.prestashop.instance.eventEmitter;

    super($(searchInputContainer), {
      onRemovedContent: () => eventEmitter.emit(ProductEventMap.updateSubmitButtonState),
      onSelectedContent: () => eventEmitter.emit(ProductEventMap.updateSubmitButtonState),
      suggestionTemplate: (product: Product) => {
        let reference = '';

        if (product.reference) {
          reference = `<span class="combination-reference">(${product.reference})</span>`;
        }

        return `<div class="search-suggestion"><img src="${product.image}" /> ${product.name}${reference}</div>`;
      },
      responseTransformer: (response: any) => {
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
      },
    });
  }
}
