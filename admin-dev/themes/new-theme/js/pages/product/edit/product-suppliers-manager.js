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
        this.append({
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

  append(supplier) {
    if (typeof this.suppliers[supplier.id] === 'undefined') {
      const rowPrototype = new DOMParser().parseFromString(
        this.$supplierReferencesBlock.data('prototype'),
        'text/html',
      );

      this.suppliers[supplier.id] = {
        deleted: false,
        supplierId: supplier.id,
        supplierName: supplier.name,
        productSupplierId: rowPrototype.getElementById(
          'product_suppliers_supplier_references___SUPPLIER_ID___product_supplier_product_supplier_id').value,
        reference: rowPrototype.getElementById(
          'product_suppliers_supplier_references___SUPPLIER_ID___product_supplier_supplier_reference').value,
        price: rowPrototype.getElementById(
          'product_suppliers_supplier_references___SUPPLIER_ID___product_supplier_supplier_price_tax_excluded').value,
        currencyId: rowPrototype.getElementById(
          'product_suppliers_supplier_references___SUPPLIER_ID___product_supplier_currency_id').value,
      };
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
        .replace(/__SUPPLIER_ID__/g, supplier.supplierId)
        // .replace(/__SUPPLIER_REFERENCE_INDEX__/g, index)
        .replace(/__SUPPLIER_NAME__/g, supplier.supplierName);

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
        this.checkFirstAvailableDefaultSupplier(selectedSupplierIds);
      }
      input.disabled = !isValid;
    });
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
}
