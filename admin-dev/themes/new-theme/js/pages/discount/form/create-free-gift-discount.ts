/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EntitySearchInput from '@components/entity-search-input';
import DiscountMap from '@pages/discount/discount-map';

const {$} = window;
export default class CreateFreeGiftDiscount {
  $freeGiftSearchInput: JQuery;

  entitySearchInput!: EntitySearchInput;

  constructor() {
    this.$freeGiftSearchInput = $(DiscountMap.freeGiftProductSearchContainer);

    if (this.$freeGiftSearchInput.length) {
      const autocompleteUrl = (document.querySelector(DiscountMap.freeGiftProductSearchContainer) as HTMLElement)
        ?.dataset.remoteUrl;
      this.entitySearchInput = new EntitySearchInput(
        this.$freeGiftSearchInput,
        {remoteUrl: autocompleteUrl},
      );
    }
  }
}
