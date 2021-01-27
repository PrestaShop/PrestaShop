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

    this.init();
  }

  init() {
    this.toggleTableVisibility();
    this.refreshDefaultSupplierBlock();

    this.$supplierSelectionBlock.on('change', 'input', (e) => {
      const input = e.currentTarget;
      if (input.checked) {
        this.appendRow({
          id: input.value,
          name: input.dataset.label,
        });
      } else {
        this.removeRow(input.value);
      }

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

  appendRow(supplier) {
    const productSupplierRowPrototype = this.$supplierReferencesBlock.data('prototype');
    const index = this.getSelectedSuppliers().length - 1;

    const productSupplierRow = productSupplierRowPrototype
      .replace(/__SUPPLIER_ID__/g, supplier.id)
      .replace(/__SUPPLIER_REFERENCE_INDEX__/g, index)
      .replace(/__SUPPLIER_NAME__/g, supplier.name);

    this.$productSuppliersTBody.append(productSupplierRow);
    // Fill hidden inputs
    $(ProductMap.supplierReferenceSupplierIdInput(index)).val(supplier.id);
    $(ProductMap.supplierReferenceSupplierNameInput(index)).val(supplier.name);

    const selectedDefaultSupplierInput = $(ProductMap.selectedDefaultSupplierInput);
    const selectedDefaultSupplierId = selectedDefaultSupplierInput.val();
    $(ProductMap.supplierReferenceIsDefaultInput(index)).val(selectedDefaultSupplierId === supplier.id);
  }

  removeRow(supplierId) {
    const supplierRow = $(ProductMap.supplierReferenceProductSupplierRow(supplierId));
    supplierRow.remove();
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
}
