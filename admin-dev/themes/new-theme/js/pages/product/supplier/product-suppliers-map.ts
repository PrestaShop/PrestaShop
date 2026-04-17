/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default (productSuppliersId: string): Record<string, any> => {
  // eslint-disable-next-line max-len
  const productSupplierInputId = (supplierIndex: string, inputName: string): string => `${productSuppliersId}_${supplierIndex}_${inputName}`;

  return {
    productSuppliersCollection: `${productSuppliersId}`,
    productSuppliersCollectionRow: '.product-suppliers-collection-row',
    productSuppliersTable: `${productSuppliersId} table`,
    productsSuppliersTableBody: `${productSuppliersId} table tbody`,
    productsSuppliersRows: `${productSuppliersId} table tbody .product_supplier_row`,
    productsSupplierRowSelector: '.product_supplier_row',
    productSupplierRow: {
      supplierIdInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'supplier_id'),
      supplierNameInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'supplier_name'),
      productSupplierIdInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'product_supplier_id'),
      referenceInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'reference'),
      priceInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'price_tax_excluded'),
      currencyIdInput: (supplierIndex: string): string => productSupplierInputId(supplierIndex, 'currency_id'),
      supplierNamePreview: (supplierIndex: string): string => `#product_supplier_row_${supplierIndex} .supplier_name .preview`,
      currencySymbol: (supplierIndex: string): string => `#product_supplier_row_${supplierIndex} .money-type .input-group-text`,
    },
  };
};
