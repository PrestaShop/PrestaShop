import ProductMap from '@pages/product/product-map';

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

const {$} = window;

export default class ProductSuppliersManager {
  constructor() {
    this.$supplierSelectionBlock = $(ProductMap.supplierSelectionBlock);
    this.$supplierReferencesBlock = $(ProductMap.supplierReferencesBlock);
    this.$productSuppliersTable = $(ProductMap.productSuppliersTable);
    this.$productSuppliersTBody = this.$productSuppliersTable.find('tbody');
    this.$defaultSuppliersSelectionBlock = $(ProductMap.defaultSupplierSelectionBlock);
    this.suppliers = [];
    this.newSupplierData = this.collectDataForNewSupplier();

    this.init();
  }

  init() {
    this.collectCurrentSuppliers();
    this.toggleTableVisibility();
    this.refreshDefaultSupplierBlock();

    this.$productSuppliersTable.on('change', 'input', () => {
      this.collectCurrentSuppliers();
    });

    this.$supplierSelectionBlock.on('change', 'input', (e) => {
      const input = e.currentTarget;
      if (input.checked) {
        this.add({
          id: input.value,
          name: input.dataset.label,
        });
      } else {
        this.remove(input.value);
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

  add(supplier) {
    if (typeof this.suppliers[supplier.id] === 'undefined') {
      const newSupplier = Object.create(this.newSupplierData);
      newSupplier.supplierId = supplier.id;
      newSupplier.supplierName = supplier.name;

      this.suppliers[supplier.id] = newSupplier;
    } else {
      this.suppliers[supplier.id].deleted = false;
    }
  }

  remove(supplierId) {
    this.suppliers[supplierId].deleted = true;
  }

  renderSuppliers() {
    this.$productSuppliersTBody.empty();
    const productSupplierRowPrototype = this.$supplierReferencesBlock.data('prototype');

    this.suppliers.forEach((supplier) => {
      if (supplier.deleted) {
        return;
      }

      const productSupplierRow = productSupplierRowPrototype
        .replace(new RegExp(ProductMap.supplierIdPlaceholder, 'g'), supplier.supplierId)
        .replace(new RegExp(ProductMap.supplierNamePlaceholder, 'g'), supplier.supplierName);

      this.$productSuppliersTBody.append(productSupplierRow);
      // Fill inputs
      $(ProductMap.suppliersSupplierIdInput(supplier.supplierId)).val(supplier.supplierId);
      $(ProductMap.suppliersProductSupplierIdInput(supplier.supplierId)).val(supplier.productSupplierId);
      $(ProductMap.suppliersSupplierNameInput(supplier.supplierId)).val(supplier.supplierName);
      $(ProductMap.suppliersProductSupplierReferenceInput(supplier.supplierId)).val(supplier.reference);
      $(ProductMap.suppliersProductSupplierPriceInput(supplier.supplierId)).val(supplier.price);
      $(ProductMap.suppliersProductSupplierCurrencyIdInput(supplier.supplierId)).val(supplier.currencyId);
    });
  }

  getSelectedSuppliers() {
    const selectedSuppliers = [];
    this.$supplierSelectionBlock.find('input:checked').each((index, input) => {
      selectedSuppliers.push({
        name: input.dataset.label,
        id: input.value,
      });
    });

    return selectedSuppliers;
  }

  refreshDefaultSupplierBlock() {
    const suppliers = this.getSelectedSuppliers();
    if (suppliers.length === 0) {
      this.$defaultSuppliersSelectionBlock.find('input').prop('checked', false);
      this.hideDefaultSuppliers();

      return;
    }

    this.showDefaultSuppliers();
    const selectedSupplierIds = suppliers.map((supplier) => supplier.id);

    this.$defaultSuppliersSelectionBlock.find('input').each((key, input) => {
      const isValid = selectedSupplierIds.includes(input.value);
      if (!isValid && input.checked) {
        input.checked = false;
      }
      input.disabled = !isValid;
    });

    if (this.$defaultSuppliersSelectionBlock.find('input:checked').length === 0) {
      this.checkFirstAvailableDefaultSupplier(selectedSupplierIds);
    }
  }

  hideDefaultSuppliers() {
    this.$defaultSuppliersSelectionBlock.addClass('d-none');
  }

  showDefaultSuppliers() {
    this.$defaultSuppliersSelectionBlock.removeClass('d-none');
  }

  checkFirstAvailableDefaultSupplier(selectedSupplierIds) {
    const firstSupplierId = selectedSupplierIds[0];
    this.$defaultSuppliersSelectionBlock.find(`input[value="${firstSupplierId}"]`).prop('checked', true);
  }

  showTable() {
    this.$productSuppliersTable.removeClass('d-none');
  }

  hideTable() {
    this.$productSuppliersTable.addClass('d-none');
  }

  collectCurrentSuppliers() {
    this.getSelectedSuppliers().forEach((supplier) => {
      this.suppliers[supplier.id] = {
        supplierId: $(ProductMap.suppliersSupplierIdInput(supplier.id)).val(),
        productSupplierId: $(ProductMap.suppliersProductSupplierIdInput(supplier.id)).val(),
        supplierName: $(ProductMap.suppliersSupplierNameInput(supplier.id)).val(),
        reference: $(ProductMap.suppliersProductSupplierReferenceInput(supplier.id)).val(),
        price: $(ProductMap.suppliersProductSupplierPriceInput(supplier.id)).val(),
        currencyId: $(ProductMap.suppliersProductSupplierCurrencyIdInput(supplier.id)).val(),
        deleted: false,
      };
    });
  }

  collectDataForNewSupplier() {
    const rowPrototype = new DOMParser().parseFromString(
      this.$supplierReferencesBlock.data('prototype'),
      'text/html',
    );

    const idPlaceholder = ProductMap.supplierIdPlaceholder;

    return {
      deleted: false,
      productSupplierId:
        rowPrototype.querySelector(ProductMap.suppliersProductSupplierIdInput(idPlaceholder)).value,
      reference:
        rowPrototype.querySelector(ProductMap.suppliersProductSupplierReferenceInput(idPlaceholder)).value,
      price:
        rowPrototype.querySelector(ProductMap.suppliersProductSupplierPriceInput(idPlaceholder)).value,
      currencyId: rowPrototype.querySelector(ProductMap.suppliersProductSupplierCurrencyIdInput(idPlaceholder)).value,
    };
  }
}
