import {getSpecificPrices} from '@pages/product/services/specific-price-service';
import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';

const SpecificPriceMap = ProductMap.specificPrice;

export default class SpecificPriceList {
  eventEmitter: EventEmitter;

  productId: number;

  listContainer: HTMLElement

  constructor(
    productId: number,
  ) {
    this.productId = productId;
    this.listContainer = document.querySelector(SpecificPriceMap.listContainer) as HTMLElement;
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.renderList();
  }

  public renderList(): void {
    const {listFields} = SpecificPriceMap;
    const tbody = this.listContainer.querySelector(`${SpecificPriceMap.listContainer} tbody`) as HTMLElement;
    const trTemplate = this.listContainer.querySelector(SpecificPriceMap.listRowTemplate) as HTMLTemplateElement;
    tbody.innerHTML = '';

    getSpecificPrices(this.productId).then((response) => {
      const specificPrices = response.specificPrices as Array<SpecificPriceForListing>;

      specificPrices.forEach((specificPrice: SpecificPriceForListing) => {
        const trClone = trTemplate.content.cloneNode(true) as HTMLElement;
        const idField = this.selectListField(trClone, listFields.specificPriceId);
        const combinationField = this.selectListField(trClone, listFields.combination);
        const currencyField = this.selectListField(trClone, listFields.currency);
        const countryField = this.selectListField(trClone, listFields.country);
        const groupField = this.selectListField(trClone, listFields.group);
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
      });
    });
  }

  private selectListField(templateTrClone: HTMLElement, selector: string): HTMLElement {
    return templateTrClone.querySelector(selector) as HTMLElement;
  }
}
