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

import SuppliersMap from '@pages/product/suppliers-map';
import ProductFormModel from '@pages/product/edit/product-form-model';

const {$} = window;

export default class ProductSuppliersManager {
  forceUpdateDefault: boolean;

  suppliersMap: Record<string, any>;

  $productSuppliersCollection: JQuery;

  $defaultSupplierGroup: JQuery;

  $supplierIdsGroup: JQuery;

  $productsTable: JQuery;

  $productsTableBody: JQuery;

  suppliers: Array<Record<string, any>>;

  prototypeTemplate: string;

  prototypeName: string;

  defaultDataForSupplier: Record<string, any>;

  $initialDefault!: JQuery;

  productFormModel!: ProductFormModel | null | undefined;

  /**
   *
   * @param {string} suppliersFormId
   * @param {boolean} forceUpdateDefault
   * @param {ProductFormModel} productFormModel
   *
   * @returns {{}}
   *
   * @todo: productFormModel should not be used for combination suppliers.
   *        It is now also initialized for combinations as temporary fix to, but should be fixed in other PR.
   *        Dedicated issue- https://github.com/PrestaShop/PrestaShop/issues/26906
   */
  constructor(suppliersFormId: string, forceUpdateDefault: boolean, productFormModel: ProductFormModel | null = null) {
    this.productFormModel = productFormModel;
    this.forceUpdateDefault = forceUpdateDefault;
    this.suppliersMap = SuppliersMap(suppliersFormId);
    this.$productSuppliersCollection = $(this.suppliersMap.productSuppliersCollection);
    this.$supplierIdsGroup = $(this.suppliersMap.supplierIdsInput).closest('.form-group');
    this.$defaultSupplierGroup = $(this.suppliersMap.defaultSupplierInput).closest('.form-group');
    this.$productsTable = $(this.suppliersMap.productSuppliersTable);
    this.$productsTableBody = $(this.suppliersMap.productsSuppliersTableBody);

    this.suppliers = [];
    this.prototypeTemplate = this.$productSuppliersCollection.data('prototype');
    this.prototypeName = this.$productSuppliersCollection.data('prototypeName');
    this.defaultDataForSupplier = this.getDefaultDataForSupplier();

    this.init();
  }

  private init(): void {
    this.memorizeCurrentSuppliers();
    this.toggleTableVisibility();
    this.refreshDefaultSupplierBlock();

    this.$initialDefault = this.$defaultSupplierGroup.find('input:checked').first();
    if (this.$initialDefault.length) {
      this.$initialDefault
        .closest(this.suppliersMap.checkboxContainer)
        .addClass(this.suppliersMap.defaultSupplierClass);
    }

    this.$productsTable.on('change', 'input', () => {
      this.memorizeCurrentSuppliers();
    });

    this.$supplierIdsGroup.on('change', 'input', (e) => {
      const input = e.currentTarget;

      if (input.checked) {
        this.addSupplier({
          supplierId: input.value,
          supplierName: input.dataset.label,
        });
      } else {
        this.removeSupplier(input.value);
      }

      this.renderSuppliers();
      this.toggleTableVisibility();
      this.refreshDefaultSupplierBlock();
    });

    // Default supplier changed
    this.$defaultSupplierGroup.on('change', 'input', () => {
      this.updateProductWholesalePrice();
    });

    if (this.productFormModel) {
      this.productFormModel.watchProductModel('price.wholesalePrice', (event) => {
        this.updateDefaultProductSupplierPrice(event.value);
      });
    }
  }

  /**
   * @param {string} newPrice
   */
  updateDefaultProductSupplierPrice(newPrice: number): void {
    const defaultProductSupplierId = this.getDefaultProductSupplierId();

    if (defaultProductSupplierId) {
      // Update default price value and trigger change so that memorizeCurrentSuppliers is triggered (along with other
      // potential listeners)
      const rowMap = this.suppliersMap.productSupplierRow;
      $(rowMap.priceInput(defaultProductSupplierId)).val(newPrice).trigger('change');
    }
  }

  updateProductWholesalePrice(): void {
    const defaultProductSupplierId = this.getDefaultProductSupplierId();

    if (defaultProductSupplierId) {
      const $defaultPriceInput = $(this.suppliersMap.productSupplierRow.priceInput(defaultProductSupplierId));

      if (!$defaultPriceInput.length) {
        return;
      }
      const newDefaultPrice = $defaultPriceInput.val();

      if (this.productFormModel) {
        this.productFormModel.setProductValue('price.wholesalePrice', newDefaultPrice);
      }
    }
  }

  /**
   * @returns {null|int}
   */
  getDefaultProductSupplierId(): string | number | string[] | undefined | null {
    if (this.getSelectedSuppliers().length === 0) {
      return null;
    }

    const defaultSupplier = this.$defaultSupplierGroup.find('input:checked');

    if (!defaultSupplier.length) {
      return null;
    }

    return defaultSupplier.first().val();
  }

  private toggleTableVisibility(): void {
    if (this.getSelectedSuppliers().length === 0) {
      this.hideTable();

      return;
    }

    this.showTable();
  }

  /**
   * @param {Object} supplier
   *
   * @private
   */
  private addSupplier(supplier: Record<string, any>): void {
    if (this.productFormModel) {
      const wholeSalePrice = this.productFormModel.getProduct().price.wholesalePrice;

      if (typeof this.suppliers[supplier.supplierId] === 'undefined') {
        const newSupplier = Object.create(this.defaultDataForSupplier);
        newSupplier.supplierId = supplier.supplierId;
        newSupplier.supplierName = supplier.supplierName;
        newSupplier.price = wholeSalePrice;

        this.suppliers[supplier.supplierId] = newSupplier;
      } else {
        this.suppliers[supplier.supplierId].removed = false;
      }
    }
  }

  /**
   * @param {int} supplierId
   *
   * @private
   */
  private removeSupplier(supplierId: number): void {
    this.suppliers[supplierId].removed = true;
  }

  private renderSuppliers(): void {
    this.$productsTableBody.empty();

    // Loop through select suppliers so that we use the same order as in the select list
    this.getSelectedSuppliers().forEach((selectedSupplier) => {
      const supplier = this.suppliers[selectedSupplier.supplierId];

      if (supplier.removed) {
        return;
      }

      const productSupplierRow = this.prototypeTemplate.replace(
        new RegExp(this.prototypeName, 'g'),
        supplier.supplierId,
      );

      this.$productsTableBody.append(productSupplierRow);
      // Fill inputs
      const rowMap = this.suppliersMap.productSupplierRow;
      $(rowMap.supplierIdInput(supplier.supplierId)).val(supplier.supplierId);
      $(rowMap.supplierNamePreview(supplier.supplierId)).html(supplier.supplierName);
      $(rowMap.supplierNameInput(supplier.supplierId)).val(supplier.supplierName);
      $(rowMap.productSupplierIdInput(supplier.supplierId)).val(supplier.productSupplierId);
      $(rowMap.referenceInput(supplier.supplierId)).val(supplier.reference);
      $(rowMap.priceInput(supplier.supplierId)).val(supplier.price);
      $(rowMap.currencyIdInput(supplier.supplierId)).val(supplier.currencyId);
    });
  }

  private getSelectedSuppliers(): Array<Record<string, any>> {
    const selectedSuppliers: Array<Record<string, any>> = [];
    this.$supplierIdsGroup.find('input:checked').each((index, input) => {
      const inputElement = <HTMLInputElement> input;

      selectedSuppliers.push({
        supplierName: inputElement.dataset.label,
        supplierId: inputElement.value,
      });
    });

    return selectedSuppliers;
  }

  private refreshDefaultSupplierBlock(): void {
    const suppliers = this.getSelectedSuppliers();

    if (suppliers.length === 0) {
      if (this.forceUpdateDefault) {
        this.$defaultSupplierGroup.find('input').prop('checked', false);
      }
      this.hideDefaultSuppliers();

      return;
    }

    this.showDefaultSuppliers();
    const selectedSupplierIds = suppliers.map((supplier) => supplier.supplierId);

    this.$defaultSupplierGroup.find('input').each((key, input) => {
      const inputElement = <HTMLInputElement> input;
      const isValid = selectedSupplierIds.includes(input.value);

      if (this.forceUpdateDefault && !isValid) {
        inputElement.checked = false;
      }
      inputElement.disabled = !isValid;
    });

    if (this.$defaultSupplierGroup.find('input:checked').length === 0 && this.forceUpdateDefault) {
      this.checkFirstAvailableDefaultSupplier(selectedSupplierIds);
    }
  }

  private hideDefaultSuppliers(): void {
    this.$defaultSupplierGroup.addClass('d-none');
  }

  private showDefaultSuppliers(): void {
    this.$defaultSupplierGroup.removeClass('d-none');
  }

  /**
   * @param {int[]} selectedSupplierIds
   *
   * @private
   */
  private checkFirstAvailableDefaultSupplier(selectedSupplierIds: Array<number>): void {
    const firstSupplierId = selectedSupplierIds[0];
    this.$defaultSupplierGroup.find(`input[value="${firstSupplierId}"]`).prop('checked', true);
  }

  private showTable(): void {
    this.$productsTable.removeClass('d-none');
  }

  private hideTable(): void {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Memorize suppliers to be able to re-render them later.
   * Flag `removed` allows identifying whether supplier was removed from list or should be rendered
   *
   * @private
   */
  private memorizeCurrentSuppliers(): void {
    this.getSelectedSuppliers().forEach((supplier) => {
      this.suppliers[supplier.supplierId] = {
        supplierId: supplier.supplierId,
        productSupplierId: $(this.suppliersMap.productSupplierRow.productSupplierIdInput(supplier.supplierId)).val(),
        supplierName: $(this.suppliersMap.productSupplierRow.supplierNameInput(supplier.supplierId)).val(),
        reference: $(this.suppliersMap.productSupplierRow.referenceInput(supplier.supplierId)).val(),
        price: $(this.suppliersMap.productSupplierRow.priceInput(supplier.supplierId)).val(),
        currencyId: $(this.suppliersMap.productSupplierRow.currencyIdInput(supplier.supplierId)).val(),
        removed: false,
      };
    });

    this.updateProductWholesalePrice();
  }

  /**
   * Create a "shadow" prototype just to parse default values set inside the input fields,
   * this allow to build an object with default values set in the FormType
   *
   * @private
   *
   * @returns {{reference, removed: boolean, price, currencyId, productSupplierId}}
   */
  private getDefaultDataForSupplier(): Record<string, any> {
    const rowPrototype = new DOMParser().parseFromString(
      this.prototypeTemplate,
      'text/html',
    );

    return {
      removed: false,
      productSupplierId: this.getDataFromRow(this.suppliersMap.productSupplierRow.productSupplierIdInput, rowPrototype),
      reference: this.getDataFromRow(this.suppliersMap.productSupplierRow.referenceInput, rowPrototype),
      price: this.getDataFromRow(this.suppliersMap.productSupplierRow.priceInput, rowPrototype),
      currencyId: this.getDataFromRow(this.suppliersMap.productSupplierRow.currencyIdInput, rowPrototype),
    };
  }

  /**
   * @param selectorGenerator {function}
   * @param rowPrototype {Document}
   *
   * @private
   *
   * @returns {*}
   */
  private getDataFromRow(selectorGenerator: (name: string) => string, rowPrototype: Document): any {
    return (<HTMLInputElement> rowPrototype?.querySelector(selectorGenerator(this.prototypeName)))?.value;
  }
}
