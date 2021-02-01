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

export default {
  productForm: 'form[name=product]',
  suppliersBlock: '#productSuppliers',
  supplierReferencesBlock: '#product_suppliers_supplier_references',
  supplierReferencesContainer: '#product_suppliers_supplier_references > .col-sm',
  productSuppliersTable: '#productSuppliers table',
  supplierSelectionBlock: '#product_suppliers_supplier_ids',
  defaultSupplierSelectionBlock: '#defaultSupplierIdBlock',
  selectedDefaultSupplierInput: 'input[name="product[suppliers][default_supplier_id]"]:checked',
  productFormSubmitButton: 'button[name="product[save]"]',
  suppliersProductSupplierRow: (supplierId) => `#product_supplier_row_${supplierId}`,
  suppliersSupplierIdInput: (index) => `#product_suppliers_supplier_references_${index}_supplier_id`,
  suppliersSupplierNameInput: (index) => `#product_suppliers_supplier_references_${index}_supplier_name`,
  suppliersProductSupplierIdInput:
    (index) => `#product_suppliers_supplier_references_${index}_product_supplier_product_supplier_id`,
  suppliersProductSupplierReferenceInput:
    (index) => `#product_suppliers_supplier_references_${index}_product_supplier_supplier_reference`,
  suppliersProductSupplierPriceInput:
    (index) => `#product_suppliers_supplier_references_${index}_product_supplier_supplier_price_tax_excluded`,
  suppliersProductSupplierCurrencyIdInput:
    (index) => `#product_suppliers_supplier_references_${index}_product_supplier_currency_id`,
};
