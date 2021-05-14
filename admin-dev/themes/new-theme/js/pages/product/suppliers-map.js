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

export default (suppliersFormId) => {
  const productSuppliersId = `${suppliersFormId}_product_suppliers`;
  const productSupplierInputId = (supplierIndex, inputName) => `${productSuppliersId}_${supplierIndex}_${inputName}`;

  return {
    productSuppliersCollection: `${productSuppliersId}`,
    supplierIdsInput: `${suppliersFormId}_supplier_ids`,
    defaultSupplierInput: `${suppliersFormId}_default_supplier_id`,
    productSuppliersTable: `${productSuppliersId} table`,
    productsSuppliersTableBody: `${productSuppliersId} table tbody`,
    defaultSupplierClass: 'default-supplier',
    productSupplierRow: {
      supplierIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'supplier_id'),
      supplierNameInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'supplier_name'),
      productSupplierIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'product_supplier_id'),
      referenceInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'reference'),
      priceInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'price_tax_excluded'),
      currencyIdInput: (supplierIndex) => productSupplierInputId(supplierIndex, 'currency_id'),
      supplierNamePreview: (supplierIndex) => `#product_supplier_row_${supplierIndex} .supplier_name .preview`,
    },
    checkboxContainer: '.form-check',
  };
};
