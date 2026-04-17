/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';

const {$} = window;

export default class ImageSelector {
  $selectorContainer: JQuery;

  constructor() {
    this.$selectorContainer = $(ProductMap.combinations.images.selectorContainer);
    this.init();
  }

  private init(): void {
    $(ProductMap.combinations.images.checkboxContainer, this.$selectorContainer).hide();
    this.$selectorContainer.on('click', ProductMap.combinations.images.imageChoice, (event) => {
      if (this.$selectorContainer.hasClass('disabled')) {
        return;
      }
      const $imageChoice = $(event.currentTarget);
      const $checkbox = $(ProductMap.combinations.images.checkbox, $imageChoice);

      const isChecked = $checkbox.prop('checked');
      $imageChoice.toggleClass('selected', !isChecked);
      $checkbox.prop('checked', !isChecked);
    });
  }
}
