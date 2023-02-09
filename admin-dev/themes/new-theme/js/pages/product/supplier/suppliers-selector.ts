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
