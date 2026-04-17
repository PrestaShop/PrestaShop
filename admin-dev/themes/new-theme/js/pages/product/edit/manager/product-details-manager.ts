/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import ProductMap from '@pages/product/product-map';

export default class ProductDetailsManager {
  constructor() {
    this.initConditionToggler();
  }

  private initConditionToggler(): void {
    new FormFieldToggler({
      disablingInputSelector: ProductMap.conditionSwitch,
      targetSelector: ProductMap.conditionChoiceSelect,
      toggleType: ToggleType.availability,
    });
  }
}
