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

    this.$supplierReferencesBlock.empty();

    selectedSuppliers.forEach((supplier) => {
      const supplierReferencesContent = supplierReferencesPrototype.replace(/__SUPPLIER_ID__/g, supplier.id)
        .replace(/__SUPPLIER_NAME__/g, supplier.name);

      this.$supplierReferencesBlock.append(supplierReferencesContent);

      const productSupplierPrototype = this.$supplierReferencesBlock
        .find(`#product_suppliers_supplier_references_${supplier.id}_product_suppliers_collection`).data('prototype');

      const $productSuppliersTbody = this.$supplierReferencesBlock.find(`#supplier_${supplier.id} table tbody`);

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
