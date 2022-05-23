import FormFieldToggler, {ToggleType} from '@components/form/form-field-toggler';
import ProductSuppliersCollection from '@pages/product/components/suppliers/product-suppliers-collection';
import {ProductSupplier, Supplier} from '@pages/product/components/suppliers/supplier-types';
import SuppliersSelector from '@pages/product/components/suppliers/suppliers-selector';
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
    this.manageSuppliers();
  }

  private initShowPriceToggler(): void {
    new FormFieldToggler({
      disablingInputSelector: ProductMap.options.availableForOrderInput,
      matchingValue: '0',
      disableOnMatch: true,
      targetSelector: ProductMap.options.showPriceSwitchContainer,
      toggleType: ToggleType.visibility,
    });
  }

  private manageSuppliers(): void {
    let productSuppliers: ProductSuppliersCollection;

    if (this.productType !== ProductConst.PRODUCT_TYPE.COMBINATIONS) {
      productSuppliers = new ProductSuppliersCollection(
        ProductMap.suppliers.productSuppliers,
        this.productFormModel.getProduct().suppliers?.defaultSupplierId || 0,
        this.productFormModel.getProduct().price.wholesalePrice,
        (defaultProductSupplier: ProductSupplier) => {
          this.productFormModel.set('price.wholesalePrice', defaultProductSupplier.price);
        },
      );

      this.productFormModel.watch('price.wholesalePrice', (event) => {
        productSuppliers.updateWholesalePrice(event.value);
      });
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
