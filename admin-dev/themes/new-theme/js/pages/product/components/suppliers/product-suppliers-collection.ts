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

import ProductFormModel from '@pages/product/edit/product-form-model';
import ProductSuppliersMap from '@pages/product/components/suppliers/product-suppliers-map';
import {Supplier, ProductSupplier, DefaultProductSupplier} from '@pages/product/components/suppliers/supplier-types';

export default class ProductSuppliersCollection {
  private readonly productFormModel: ProductFormModel;

  private map: any;

  private $productSuppliersCollection: JQuery;

  private $productsTable: JQuery;

  private $productsTableBody: JQuery;

  private readonly prototypeTemplate: string;

  private readonly prototypeName: string;

  private selectedSuppliers: Array<Supplier>;

  private readonly productSuppliers: Record<string, ProductSupplier>;

  private readonly defaultDataForSupplier: DefaultProductSupplier;

  constructor(productSuppliersFormId: string, productFormModel: ProductFormModel) {
    this.productFormModel = productFormModel;
    this.map = ProductSuppliersMap(productSuppliersFormId);
    this.$productSuppliersCollection = $(this.map.productSuppliersCollection);
    this.$productsTable = $(this.map.productSuppliersTable);
    this.$productsTableBody = $(this.map.productsSuppliersTableBody);

    this.selectedSuppliers = [];
    this.productSuppliers = {};
    this.prototypeTemplate = this.$productSuppliersCollection.data('prototype');
    this.prototypeName = this.$productSuppliersCollection.data('prototypeName');
    this.defaultDataForSupplier = this.getDefaultDataForSupplier();

    this.init();
  }

  setSelectedSuppliers(selectedSuppliers: Array<Supplier>): void {
    this.selectedSuppliers = selectedSuppliers;

    // First add product suppliers
    const selectedSupplierIds: string[] = [];
    this.selectedSuppliers.forEach((supplier: Supplier) => {
      selectedSupplierIds.push(supplier.supplierId);
      this.addSupplier(supplier);
    });

    // Then remove the unselected ones
    const storedSupplierIds = Object.keys(this.productSuppliers);
    storedSupplierIds.forEach((supplierId: string) => {
      if (!selectedSupplierIds.includes(supplierId)) {
        this.removeSupplier(supplierId);
      }
    });

    this.memorizeCurrentSuppliers();
    this.renderSuppliers();
    this.toggleTableVisibility();
  }

  updateDefaultProductSupplierPrice(newPrice: number): void {
    const defaultProductSupplier: Supplier | undefined = this.getDefaultSupplier();

    if (defaultProductSupplier) {
      // Update default price value and trigger change so that memorizeCurrentSuppliers is triggered (along with other
      // potential listeners)
      const rowMap = this.map.productSupplierRow;
      $(rowMap.priceInput(<string> defaultProductSupplier.supplierId)).val(newPrice).trigger('change');
    }
  }

  private init(): void {
    this.memorizeCurrentSuppliers();

    this.$productsTable.on('change', 'input', () => {
      this.memorizeCurrentSuppliers();
    });
  }

  private addSupplier(supplier: Supplier): void {
    const wholeSalePrice = this.productFormModel.getProduct().price.wholesalePrice;

    if (typeof this.productSuppliers[supplier.supplierId] === 'undefined') {
      const newSupplier: ProductSupplier = Object.create(this.defaultDataForSupplier);
      newSupplier.supplierId = supplier.supplierId;
      newSupplier.supplierName = supplier.supplierName;
      newSupplier.price = wholeSalePrice;

      this.productSuppliers[supplier.supplierId] = newSupplier;
    } else {
      this.productSuppliers[supplier.supplierId].removed = false;
    }
  }

  private removeSupplier(supplierId: string): void {
    if (Object.prototype.hasOwnProperty.call(this.productSuppliers, supplierId)) {
      this.productSuppliers[supplierId].removed = true;
    }
  }

  /**
   * Memorize suppliers to be able to re-render them later.
   * Flag `removed` allows identifying whether supplier was removed from list or should be rendered
   */
  private memorizeCurrentSuppliers(): void {
    const rows = document.querySelectorAll(this.map.productsSuppliersRows);

    if (!rows.length) {
      return;
    }

    rows.forEach((row: HTMLElement) => {
      const supplierIndex: string = <string> row.dataset.supplierIndex;
      const supplierId = <string> $(this.map.productSupplierRow.supplierIdInput(supplierIndex)).val();

      this.productSuppliers[supplierId] = {
        supplierId,
        productSupplierId: <string> $(this.map.productSupplierRow.productSupplierIdInput(supplierIndex)).val(),
        supplierName: <string> $(this.map.productSupplierRow.supplierNameInput(supplierIndex)).val(),
        reference: <string> $(this.map.productSupplierRow.referenceInput(supplierIndex)).val(),
        price: <number> $(this.map.productSupplierRow.priceInput(supplierIndex)).val(),
        currencyId: <string> $(this.map.productSupplierRow.currencyIdInput(supplierIndex)).val(),
        removed: false,
      };
    });
  }

  private renderSuppliers(): void {
    this.$productsTableBody.empty();

    // Loop through select suppliers so that we use the same order as in the select list
    this.selectedSuppliers.forEach((selectedSupplier) => {
      const supplier = this.productSuppliers[selectedSupplier.supplierId];

      if (supplier.removed) {
        return;
      }

      const productSupplierRow = this.prototypeTemplate.replace(
        new RegExp(this.prototypeName, 'g'),
        <string> supplier.supplierId,
      );

      this.$productsTableBody.append(productSupplierRow);
      // Fill inputs
      const rowMap = this.map.productSupplierRow;
      $(rowMap.supplierIdInput(supplier.supplierId)).val(supplier.supplierId);
      $(rowMap.supplierNamePreview(supplier.supplierId)).html(supplier.supplierName);
      $(rowMap.supplierNameInput(supplier.supplierId)).val(supplier.supplierName);
      $(rowMap.productSupplierIdInput(supplier.supplierId)).val(supplier.productSupplierId);
      $(rowMap.referenceInput(supplier.supplierId)).val(supplier.reference);
      $(rowMap.priceInput(supplier.supplierId)).val(supplier.price);
      $(rowMap.currencyIdInput(supplier.supplierId)).val(supplier.currencyId);
    });
  }

  private toggleTableVisibility() {
    if (this.selectedSuppliers.length === 0) {
      this.hideTable();

      return;
    }

    this.showTable();
  }

  private showTable(): void {
    this.$productsTable.removeClass('d-none');
  }

  private hideTable(): void {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Create a "shadow" prototype just to parse default values set inside the input fields,
   * this allow to build an object with default values set in the FormType
   */
  private getDefaultDataForSupplier(): DefaultProductSupplier {
    const rowPrototype = new DOMParser().parseFromString(
      this.prototypeTemplate,
      'text/html',
    );

    return {
      removed: false,
      productSupplierId: <string> this.extractFromPrototype(this.map.productSupplierRow.productSupplierIdInput, rowPrototype),
      reference: <string> this.extractFromPrototype(this.map.productSupplierRow.referenceInput, rowPrototype),
      price: <number> this.extractFromPrototype(this.map.productSupplierRow.priceInput, rowPrototype),
      currencyId: <string> this.extractFromPrototype(this.map.productSupplierRow.currencyIdInput, rowPrototype),
    };
  }

  private extractFromPrototype(selector: (supplierIndex: string) => string, rowPrototype: Document): number | string | null {
    const rowField: HTMLInputElement | null = rowPrototype.querySelector(selector(this.prototypeName));

    return rowField ? rowField.value : null;
  }

  private getDefaultSupplier(): Supplier| undefined {
    return this.selectedSuppliers.find((supplier: Supplier) => supplier.isDefault);
  }
}
