import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import ProductMap from '@pages/product/product-map';

/**
 * Manages product Options tab related components
 */
export default class ProductOptionsManager {
  constructor() {
    this.init();
  }

  private init(): void {
    new FormFieldToggler({
      disablingInputSelector: ProductMap.options.availableForOrderInput,
      matchingValue: '0',
      disableOnMatch: true,
      targetSelector: ProductMap.options.showPriceSwitchContainer,
      toggleType: ToggleType.visibility,
    });
  }
}
