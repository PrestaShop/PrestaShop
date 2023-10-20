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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import {deleteSpecificPrice} from '@pages/product/service/specific-price';
import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';
import ConfirmModal from '@components/modal/confirm-modal';
import ProductEventMap from '@pages/product/product-event-map';
import {isUndefined} from '@PSTypes/typeguard';
import RendererType from '@js/types/renderers';
import {SpecificPriceForListing} from '@pages/product/specific-price/types';

const SpecificPriceMap = ProductMap.specificPrice;

export default class SpecificPriceListRenderer implements RendererType {
  private eventEmitter: EventEmitter;

  private productId: number;

  private listContainer: HTMLElement;

  private $loadingSpinner: JQuery;

  private $listTable: JQuery;

  constructor(
    productId: number,
  ) {
    this.productId = productId;
    this.listContainer = document.querySelector(SpecificPriceMap.listContainer) as HTMLElement;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$loadingSpinner = $(ProductMap.specificPrice.loadingSpinner);
    this.$listTable = $(ProductMap.specificPrice.listTable);
  }

  public setLoading(loading: boolean): void {
    this.$loadingSpinner.toggle(loading);
    this.$listTable.toggle(!loading);
  }

  public render(data: Record<string, any>): void {
    const {listFields} = SpecificPriceMap;
    const tbody = this.listContainer.querySelector(`${SpecificPriceMap.listContainer} tbody`) as HTMLElement;
    const trTemplateContainer = this.listContainer.querySelector(SpecificPriceMap.listRowTemplate) as HTMLScriptElement;
    const trTemplate = trTemplateContainer.innerHTML as string;
    tbody.innerHTML = '';

    const specificPrices = data.specificPrices as Array<SpecificPriceForListing>;
    this.toggleListVisibility(specificPrices.length > 0);

    specificPrices.forEach((specificPrice: SpecificPriceForListing) => {
      const temporaryContainer = document.createElement('tbody');
      temporaryContainer.innerHTML = trTemplate.trim();

      const trClone = temporaryContainer.firstChild as HTMLElement;
      const idField = this.selectListField(trClone, listFields.specificPriceId);
      const combinationField = this.selectListField(trClone, listFields.combination);
      const currencyField = this.selectListField(trClone, listFields.currency);
      const countryField = this.selectListField(trClone, listFields.country);
      const groupField = this.selectListField(trClone, listFields.group);
      const shopField = this.selectListField(trClone, listFields.shop);
      const customerField = this.selectListField(trClone, listFields.customer);
      const priceField = this.selectListField(trClone, listFields.price);
      const impactField = this.selectListField(trClone, listFields.impact);
      const periodField = this.selectListField(trClone, listFields.period);
      const periodFromField = this.selectListField(trClone, listFields.from);
      const periodToField = this.selectListField(trClone, listFields.to);
      const fromQtyField = this.selectListField(trClone, listFields.fromQuantity);
      const deleteBtn = this.selectListField(trClone, listFields.deleteBtn);
      const editBtn = this.selectListField(trClone, listFields.editBtn);
      idField.textContent = String(specificPrice.id);
      combinationField.textContent = specificPrice.combination;
      currencyField.textContent = specificPrice.currency;
      countryField.textContent = specificPrice.country;
      groupField.textContent = specificPrice.group;
      shopField.textContent = specificPrice.shop;
      customerField.textContent = specificPrice.customer;
      priceField.textContent = specificPrice.price;
      impactField.textContent = specificPrice.impact;
      fromQtyField.textContent = specificPrice.fromQuantity;
      deleteBtn.dataset.specificPriceId = String(specificPrice.id);
      editBtn.dataset.specificPriceId = String(specificPrice.id);

      if (!specificPrice.period) {
        periodField.textContent = String(periodField.dataset.unlimitedText);
      } else {
        periodFromField.textContent = specificPrice.period.from;
        periodToField.textContent = specificPrice.period.to;
      }

      tbody.append(trClone);
      this.addEventListenerForDeleteBtn(deleteBtn);
    });
  }

  private toggleListVisibility(show: boolean): void {
    this.listContainer.classList.toggle('d-none', !show);
  }

  private selectListField(templateTrClone: HTMLElement, selector: string): HTMLElement {
    return templateTrClone.querySelector(selector) as HTMLElement;
  }

  private addEventListenerForDeleteBtn(deleteBtn: HTMLElement): void {
    deleteBtn.addEventListener('click', (e) => {
      if (!(e.currentTarget instanceof HTMLElement) || isUndefined(e.currentTarget.dataset.specificPriceId)) {
        return;
      }

      this.deleteSpecificPrice(e.currentTarget.dataset);
    });
  }

  private deleteSpecificPrice(deleteBtnDataset: DOMStringMap): void {
    const modal = new ConfirmModal(
      {
        id: ProductMap.specificPrice.deletionModalId,
        confirmTitle: deleteBtnDataset.confirmTitle,
        confirmMessage: deleteBtnDataset.confirmMessage,
        confirmButtonLabel: deleteBtnDataset.confirmBtnLabel,
        closeButtonLabel: deleteBtnDataset.cancelBtnLabel,
        confirmButtonClass: deleteBtnDataset.confirmBtnClass,
        closable: true,
      },
      async () => {
        if (!deleteBtnDataset.specificPriceId) {
          return;
        }
        const response = await deleteSpecificPrice(deleteBtnDataset.specificPriceId);
        $.growl({message: response.message});
        this.eventEmitter.emit(ProductEventMap.specificPrice.listUpdated);
      },
    );
    modal.show();
  }
}
