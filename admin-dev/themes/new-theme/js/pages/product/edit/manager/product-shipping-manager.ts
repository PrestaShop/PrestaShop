/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import ProductMap from '@pages/product/product-map';

export default class ProductShippingManager {
  constructor() {
    this.initDeliveryTimesToggler();
  }

  private initDeliveryTimesToggler(): void {
    new FormFieldToggler({
      disablingInputSelector: ProductMap.shipping.deliveryTimeTypeInput,
      matchingValue: '2',
      disableOnMatch: false,
      targetSelector: ProductMap.shipping.deliveryTimeNotesBlock,
      toggleType: ToggleType.availability,
    });
  }
}
