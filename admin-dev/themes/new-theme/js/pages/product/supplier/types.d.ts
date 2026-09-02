/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export type Supplier = {
  supplierId: string,
  supplierName: string,
  isDefault: boolean
}
export type ProductSupplier = {
  supplierId: string,
  productSupplierId: string,
  supplierName: string,
  reference: string,
  price: number,
  currencyId: string,
  isDefault: boolean,
  removed: boolean,
}
export type BaseProductSupplier = Omit<ProductSupplier, 'supplierId' | 'supplierName'>;
