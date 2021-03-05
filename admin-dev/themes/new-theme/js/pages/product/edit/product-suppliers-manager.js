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

import ProductMap from '@pages/product/product-map';
import EventHOC from '@components/event-hoc';

const {$} = window;

export class ProductSuppliersManager {
  constructor() {
    this.$productSuppliersCollection = $(ProductMap.suppliers.productSuppliersCollection);
    this.$supplierIdsGroup = $(ProductMap.suppliers.supplierIdsInput).closest('.form-group');
    this.$defaultSupplierGroup = $(ProductMap.suppliers.defaultSupplierInput).closest('.form-group');
    this.$productsTable = $(ProductMap.suppliers.productsTable);
    this.$productsTableBody = $(ProductMap.suppliers.productsTableBody);

    this.suppliers = [];
    this.prototypeTemplate = this.$productSuppliersCollection.data('prototype');
    this.prototypeName = this.$productSuppliersCollection.data('prototypeName');
    this.defaultDataForSupplier = this.collectDefaultDataForSupplier();

    this.init();
  }

  init() {
    this.memorizeCurrentSuppliers();
    this.toggleTableVisibility();
    this.refreshDefaultSupplierBlock();

    this.$productsTable.on('change', 'input', () => {
      this.memorizeCurrentSuppliers();
    });

    this.$supplierIdsGroup.on('change', 'input', e => {
      const input = e.currentTarget;

      if (input.checked) {
        this.addSupplier({
          id: input.value,
          name: input.dataset.label
        });
      } else {
        this.removeSupplier(input.value);
      }

      this.renderSuppliers();
      this.toggleTableVisibility();
      this.refreshDefaultSupplierBlock();
    });
  }

  toggleTableVisibility() {
    if (this.getSelectedSuppliers().length === 0) {
      this.hideTable();

      return;
    }

    this.showTable();
  }

  addSupplier(supplier) {
    if (typeof this.suppliers[supplier.id] === 'undefined') {
      const newSupplier = Object.create(this.defaultDataForSupplier);
      newSupplier.supplierId = supplier.id;
      newSupplier.supplierName = supplier.name;

      this.suppliers[supplier.id] = newSupplier;
    } else {
      this.suppliers[supplier.id].removed = false;
    }
  }

  removeSupplier(supplierId) {
    this.suppliers[supplierId].removed = true;
  }

  renderSuppliers() {
    this.$productsTableBody.empty();

    // Custom incremental index since this.suppliers uses the supplierId as key
    let supplierIndex = 0;
    this.suppliers.forEach(supplier => {
      if (supplier.removed) {
        return;
      }

      const productSupplierRow = this.prototypeTemplate.replace(new RegExp(this.prototypeName, 'g'), supplierIndex);

      this.$productsTableBody.append(productSupplierRow);
      // Fill inputs
      $(ProductMap.suppliers.productSupplierRow.supplierIdInput(supplierIndex)).val(supplier.supplierId);
      $(ProductMap.suppliers.productSupplierRow.supplierNameCell(supplierIndex)).html(supplier.supplierName);
      $(ProductMap.suppliers.productSupplierRow.supplierNameInput(supplierIndex)).val(supplier.supplierName);
      $(ProductMap.suppliers.productSupplierRow.productSupplierIdInput(supplierIndex)).val(supplier.productSupplierId);
      $(ProductMap.suppliers.productSupplierRow.referenceInput(supplierIndex)).val(supplier.reference);
      $(ProductMap.suppliers.productSupplierRow.priceInput(supplierIndex)).val(supplier.price);
      $(ProductMap.suppliers.productSupplierRow.currencyIdInput(supplierIndex)).val(supplier.currencyId);
      supplierIndex += 1;
    });
  }

  getSelectedSuppliers() {
    const selectedSuppliers = [];
    this.$supplierIdsGroup.find('input:checked').each((index, input) => {
      selectedSuppliers.push({
        name: input.dataset.label,
        id: input.value
      });
    });

    return selectedSuppliers;
  }

  refreshDefaultSupplierBlock() {
    const suppliers = this.getSelectedSuppliers();

    if (suppliers.length === 0) {
      this.$defaultSupplierGroup.find('input').prop('checked', false);
      this.hideDefaultSuppliers();

      return;
    }

    this.showDefaultSuppliers();
    const selectedSupplierIds = suppliers.map(supplier => supplier.id);

    this.$defaultSupplierGroup.find('input').each((key, input) => {
      const isValid = selectedSupplierIds.includes(input.value);

      if (!isValid && input.checked) {
        input.checked = false;
      }
      input.disabled = !isValid;
    });

    if (this.$defaultSupplierGroup.find('input:checked').length === 0) {
      this.checkFirstAvailableDefaultSupplier(selectedSupplierIds);
    }
  }

  hideDefaultSuppliers() {
    this.$defaultSupplierGroup.addClass('d-none');
  }

  showDefaultSuppliers() {
    this.$defaultSupplierGroup.removeClass('d-none');
  }

  checkFirstAvailableDefaultSupplier(selectedSupplierIds) {
    const firstSupplierId = selectedSupplierIds[0];
    this.$defaultSupplierGroup.find(`input[value="${firstSupplierId}"]`).prop('checked', true);
  }

  showTable() {
    this.$productsTable.removeClass('d-none');
  }

  hideTable() {
    this.$productsTable.addClass('d-none');
  }

  /**
   * Memorize suppliers to be able to re-render them later.
   * Flag `removed` allows identifying whether supplier was removed from list or should be rendered
   */
  memorizeCurrentSuppliers() {
    // this.getSelectedSuppliers values are pushed so it's index is 0-based, we can use it as is
    this.getSelectedSuppliers().forEach((supplier, index) => {
      this.suppliers[supplier.id] = {
        supplierId: supplier.id,
        productSupplierId: $(ProductMap.suppliers.productSupplierRow.productSupplierIdInput(index)).val(),
        supplierName: $(ProductMap.suppliers.productSupplierRow.supplierNameInput(index)).val(),
        reference: $(ProductMap.suppliers.productSupplierRow.referenceInput(index)).val(),
        price: $(ProductMap.suppliers.productSupplierRow.priceInput(index)).val(),
        currencyId: $(ProductMap.suppliers.productSupplierRow.currencyIdInput(index)).val(),
        removed: false
      };
    });
  }

  /**
   * Create a "shadow" prototype just to parse default values set inside the input fields,
   * this allow to build an object with default values set in the FormType
   *
   * @returns {{reference, removed: boolean, price, currencyId, productSupplierId}}
   */
  collectDefaultDataForSupplier() {
    const rowPrototype = new DOMParser().parseFromString(this.prototypeTemplate, 'text/html');

    return {
      removed: false,
      productSupplierId: this.collectDataFromRow(
        ProductMap.suppliers.productSupplierRow.productSupplierIdInput,
        rowPrototype
      ),
      reference: this.collectDataFromRow(ProductMap.suppliers.productSupplierRow.referenceInput, rowPrototype),
      price: this.collectDataFromRow(ProductMap.suppliers.productSupplierRow.priceInput, rowPrototype),
      currencyId: this.collectDataFromRow(ProductMap.suppliers.productSupplierRow.currencyIdInput, rowPrototype)
    };
  }

  /**
   * @param selectorGenerator {function}
   * @param rowPrototype {Document}
   *
   * @returns {*}
   */
  collectDataFromRow(selectorGenerator, rowPrototype) {
    return rowPrototype.querySelector(selectorGenerator(this.prototypeName)).value;
  }
}

export default EventHOC(ProductSuppliersManager);
