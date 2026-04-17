/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import {Supplier} from '@pages/product/supplier/types';

const {$} = window;

export default class SuppliersSelector {
  private updateSuppliersCallback?: (suppliers: Array<Supplier>) => void;

  private $supplierIdsGroup: JQuery;

  private $defaultSupplierGroup: JQuery;

  constructor(updateSuppliersCallback?: (suppliers: Array<Supplier>) => void) {
    this.$supplierIdsGroup = $(ProductMap.suppliers.supplierIdsInput).closest('.form-group');
    this.$defaultSupplierGroup = $(ProductMap.suppliers.defaultSupplierInput).closest('.form-group');
    this.updateSuppliersCallback = updateSuppliersCallback;

    this.init();
  }

  private init(): void {
    this.refreshDefaultSupplierBlock();

    this.$supplierIdsGroup.on('change', 'input', () => {
      this.refreshDefaultSupplierBlock();
      if (this.updateSuppliersCallback) {
        this.updateSuppliersCallback(this.getSelectedSuppliers());
      }
    });
  }

  getDefaultSupplier(): Supplier | null {
    const $defaultSupplier = this.$defaultSupplierGroup.find('input:checked');

    if (!$defaultSupplier.length) {
      return null;
    }

    return {
      supplierId: <string> $defaultSupplier.first().val(),
      supplierName: <string> $defaultSupplier.first().data('label'),
      isDefault: true,
    };
  }

  private getSelectedSuppliers(): Array<Supplier> {
    const defaultSupplier: Supplier | null = this.getDefaultSupplier();
    const selectedSuppliers: Supplier[] = [];
    // @ts-ignore
    this.$supplierIdsGroup.find('input:checked').each((index: number, input: HTMLInputElement) => {
      const supplierId: string = input.value;
      selectedSuppliers.push({
        supplierId,
        supplierName: <string> input.dataset.label,
        isDefault: defaultSupplier ? supplierId === defaultSupplier.supplierId : false,
      });
    });

    return selectedSuppliers;
  }

  private refreshDefaultSupplierBlock(): void {
    const suppliers = this.getSelectedSuppliers();

    if (suppliers.length === 0) {
      this.$defaultSupplierGroup.find('input').prop('checked', false);
      this.hideDefaultSuppliers();

      return;
    }

    this.showDefaultSuppliers();
    const selectedSupplierIds = suppliers.map((supplier) => supplier.supplierId);

    this.$defaultSupplierGroup.find('input').each((key: number, input: HTMLInputElement) => {
      const isValid = selectedSupplierIds.includes(input.value);

      if (!isValid) {
        // eslint-disable-next-line no-param-reassign
        input.checked = false;
      }
      // eslint-disable-next-line no-param-reassign
      input.disabled = !isValid;
    });

    if (this.$defaultSupplierGroup.find('input:checked').length === 0) {
      this.checkFirstAvailableDefaultSupplier(selectedSupplierIds);
    }
  }

  private hideDefaultSuppliers(): void {
    this.$defaultSupplierGroup.addClass('d-none');
  }

  private showDefaultSuppliers(): void {
    this.$defaultSupplierGroup.removeClass('d-none');
  }

  private checkFirstAvailableDefaultSupplier(selectedSupplierIds: Array<string>): void {
    const firstSupplierId = selectedSupplierIds[0];
    this.$defaultSupplierGroup.find(`input[value="${firstSupplierId}"]`).prop('checked', true);
  }
}
