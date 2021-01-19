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
    this.$productForm = $(ProductMap.productForm);
    this.$suppliersBlock = $(ProductMap.suppliersBlock);
    this.$supplierSelectionBlock = $(ProductMap.supplierSelectionBlock);
    this.$defaultSupplierSelectionBlock = $(ProductMap.defaultSupplierSelectionBlock);
    this.$supplierReferencesBlock = $(ProductMap.supplierReferencesBlock);
    this.$supplierReferencePrototype = $(ProductMap.supplierReferencePrototype);
    this.$productSupplierPrototype = $(ProductMap.productSupplierPrototype);
    this.supplierReferencePrototypePlaceholder = '__SUPPLIER_REFERENCE_PROTOTYPE__';
    this.productSupplierPrototypePlaceholder = '__PRODUCT_SUPPLIER_PROTOTYPE__';
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

    const $supplierReferencePrototype = $(this.$supplierReferencePrototype.html()).clone();
    const $productSupplierPrototype = $(this.$productSupplierPrototype.html()).clone();
    const supplierRefPlaceholder = this.supplierReferencePrototypePlaceholder;
    const productSupplierPlaceholder = this.productSupplierPrototypePlaceholder;

    selectedSuppliers.forEach((supplier) => {
      const $productSupplierTemplate = $productSupplierPrototype.html();
      //@todo: loop through combinations and add its index instead of 0
      const productSupplierHtml = $productSupplierTemplate.split(productSupplierPlaceholder).join('0').split(supplierRefPlaceholder).join(supplier.id);

      $supplierReferencePrototype.find(`#product_suppliers_supplier_references_${supplierRefPlaceholder}_product_suppliers_collection`).append(productSupplierHtml);
      const templateHtml = $supplierReferencePrototype.html().split(supplierRefPlaceholder).join(supplier.id);

      this.$supplierReferencesBlock.append(supplier.name);
      this.$supplierReferencesBlock.append(templateHtml);
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
