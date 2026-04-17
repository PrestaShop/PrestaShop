/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EntitySearchInput from '@components/entity-search-input';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';
import {EventEmitter} from 'events';

export default class RelatedProductsManager {
  private eventEmitter: EventEmitter;

  private entitySearchInput: EntitySearchInput;

  constructor(eventEmitter: EventEmitter) {
    this.eventEmitter = eventEmitter;
    this.entitySearchInput = new window.prestashop.component.EntitySearchInput($(ProductMap.relatedProducts.searchInput), {
      onRemovedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
      onSelectedContent: () => {
        this.eventEmitter.emit(ProductEventMap.updateSubmitButtonState);
      },
    });
  }
}
