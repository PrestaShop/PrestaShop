/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import ProductSuppliersCollection from '@pages/product/supplier/product-suppliers-collection';
import {Supplier} from '@pages/product/supplier/types';
import SuppliersSelector from '@pages/product/supplier/suppliers-selector';
import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductMap from '@pages/product/product-map';
import ProductConst from '@pages/product/constants';

/**
 * Manages product Options tab related components
 */
export default class ProductOptionsManager {
  private readonly productType: string;

  private productFormModel: ProductFormModel;

  constructor(productType: string, productFormModel: ProductFormModel) {
    this.productType = productType;
    this.productFormModel = productFormModel;
    this.init();
  }

  private init(): void {
    this.initShowPriceToggler();
    this.initSuppliers();
    this.initProductVisibilityList();
  }

  private initShowPriceToggler(): void {
    new FormFieldToggler({
      disablingInputSelector: ProductMap.options.availableForOrderInput,
      matchingValue: '1',
      disableOnMatch: true,
      targetSelector: ProductMap.options.showPriceSwitchContainer,
      toggleType: ToggleType.availability,
    });

    const availableInput = document.querySelector<HTMLInputElement>(`${ProductMap.options.availableForOrderInput}[value="1"]`);

    if (availableInput) {
      availableInput.addEventListener('change', () => {
        if (availableInput.checked) {
          this.switchShowPrice(true);
        }
      });
    }
  }

  private switchShowPrice(switchOn: boolean): void {
    const showPriceInputs: NodeListOf<HTMLInputElement> = document.querySelectorAll(ProductMap.options.showPriceInput);

    showPriceInputs.forEach((input) => {
      // eslint-disable-next-line no-param-reassign
      input.checked = input.value === '1' ? switchOn : !switchOn;
    });
  }

  private initProductVisibilityList(): void {
    const defaultSelectedInput = document.querySelector<HTMLInputElement>(`${ProductMap.options.visibilityRadio}:checked`);
    const descriptionField = document.querySelector<HTMLDivElement>(ProductMap.options.visibilityDescriptionField);

    if (descriptionField === null || defaultSelectedInput === null) {
      return;
    }

    descriptionField.innerHTML = `${defaultSelectedInput.dataset.description}`;

    const inputs = document.querySelectorAll<HTMLInputElement>(ProductMap.options.visibilityRadio) ?? [];
    inputs.forEach((input: HTMLInputElement) => {
      input.addEventListener('change', () => {
        const selectedChoiceDescription = input.dataset.description as string;

        if (input.checked) {
          descriptionField.innerHTML = selectedChoiceDescription;
        }
      });
    });
  }

  private initSuppliers(): void {
    let productSuppliers: ProductSuppliersCollection;

    if (this.productType !== ProductConst.PRODUCT_TYPE.COMBINATIONS) {
      productSuppliers = new ProductSuppliersCollection(
        ProductMap.suppliers.productSuppliers,
        this.productFormModel.getProduct().suppliers?.defaultSupplierId || 0,
        this.productFormModel.getProduct().price.wholesalePrice,
      );

      this.productFormModel.watch('suppliers.defaultSupplierId', (event) => {
        productSuppliers.setDefaultSupplierId(event.value);
      });
    }

    new SuppliersSelector((suppliers: Supplier[]) => {
      if (productSuppliers) {
        productSuppliers.setSelectedSuppliers(suppliers);
      }
    });
  }
}
