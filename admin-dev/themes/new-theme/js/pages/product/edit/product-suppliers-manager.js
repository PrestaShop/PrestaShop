import ProductMap from "@pages/product/product-map";

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
    this.$supplierReferencesContainer = $(ProductMap.supplierReferencesContainer);
    this.eventemitter = window.prestashop.instance.eventEmitter;

    this.init();
  }

  init() {
    this.$supplierSelectionBlock.on('change', 'input', () => {
      this.renderSupplierReferences();
    });
  }

  renderSupplierReferences() {
    const selectedSuppliers = this.getSelectedSuppliers();
    const supplierReferencesPrototype = this.$supplierReferencesBlock.data('prototype');

    this.$supplierReferencesContainer.empty();

    selectedSuppliers.forEach((supplier, index) => {
      // We use index as the collection key
      const supplierReferencesContent = supplierReferencesPrototype.replace(/__SUPPLIER_ID__/g, index)
        .replace(/__SUPPLIER_NAME__/g, supplier.name);

      this.$supplierReferencesContainer.append(supplierReferencesContent);
      const appendedSupplier = this.$supplierReferencesContainer.find(ProductMap.supplierReferenceProductCollection(index));

      // Fill hidden inputs
      $(ProductMap.supplierReferenceSupplierIdInput(index)).val(supplier.id);
      $(ProductMap.supplierReferenceSupplierNameInput(index)).val(supplier.name);

      const selectedDefaultSupplierInput = $(ProductMap.selectedDefaultSupplierInput);
      const selectedDefaultSupplierId = selectedDefaultSupplierInput.val();
      $(ProductMap.supplierReferenceIsDefaultInput(index)).val(selectedDefaultSupplierId === supplier.id);

      const productSupplierPrototype = appendedSupplier.data('prototype');
      const $productSuppliersTbody = this.$supplierReferencesBlock.find(`#supplier_reference_row_${index} table tbody`);

      //@todo: replace with real product combinations from ajax or where?
      const combinations = [
        {name: 'test1', id: 1},
        {name: 'test2', id: 2},
        {name: 'test3', id: 3},
      ];
      combinations.forEach((combination) => {
        const productSupplierContent = productSupplierPrototype
          .replace(/__COMBINATION_ID__/g, combination.id)
          .replace(/__PRODUCT_NAME__/g, combination.name);
        $productSuppliersTbody.append(productSupplierContent);
      });
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
}
