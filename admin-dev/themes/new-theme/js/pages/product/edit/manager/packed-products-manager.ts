/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import ProductSearchInput from '@components/form/product-search-input';
import EventEmitter from '@components/event-emitter';
import ProductEventMap from '@pages/product/product-event-map';

export default class PackedProductsManager {
  constructor(eventEmitter: typeof EventEmitter) {
    new ProductSearchInput(ProductMap.packedProducts.searchInput, {
      onRemovedContent: () => eventEmitter.emit(ProductEventMap.updateSubmitButtonState),
      onSelectedContent: () => eventEmitter.emit(ProductEventMap.updateSubmitButtonState),
    });
  }
}
