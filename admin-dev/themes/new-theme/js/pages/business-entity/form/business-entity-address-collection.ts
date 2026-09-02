/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import BusinessEntityFormMap from '@pages/business-entity/form/business-entity-form-map';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';

// @TODO: typescript-eslint adds a no-shadow there, remove it when it's fixed on their side
// eslint-disable-next-line no-shadow
enum AddressTypeEnum {
  BILLING = 'billing',
  SHIPPING = 'shipping',
}

type AddressCard = HTMLElement & {
  dataset: DOMStringMap & {
    addressIndex: string;
  };
};

export default class BusinessEntityAddressCollection {
  private selectedShippingAddress: AddressCard | null = null;

  private selectedBillingAddress: AddressCard | null = null;

  private readonly map: { [key: string]: string } = BusinessEntityFormMap;

  constructor() {
    this.attachEventListeners();
    this.initDefaultBillingAddress();
    this.initDefaultShippingAddress();
    this.initShippingVisibilityToggle();
  }

  private attachEventListeners(): void {
    document.addEventListener('click', this.handleClick);
  }

  private initShippingVisibilityToggle(): void {
    const toggles = document.querySelectorAll<HTMLInputElement>(
      this.map.businessEntityBillingAddressAsShippingToggle,
    );

    if (toggles.length === 0) {
      return;
    }

    toggles.forEach((toggle) => toggle.addEventListener('change', () => this.updateShippingFormVisibility()));
    this.updateShippingFormVisibility();
  }

  private updateShippingFormVisibility(): void {
    const checked = document.querySelector<HTMLInputElement>(
      `${this.map.businessEntityBillingAddressAsShippingToggle}:checked`,
    );

    const useBillingAsShipping = checked?.value === '1';
    const elements = document.querySelectorAll<HTMLElement>(this.map.businessEntityShippingForm);

    for (let index = 0; index < elements.length; index += 1) {
      elements[index].classList.toggle('d-none', useBillingAsShipping);
    }
  }

  private handleClick = (event: Event): void => {
    const target = event.target as HTMLElement;

    const addBtn = target.closest<HTMLElement>(
      `${this.map.addBusinessEntityBillingAddress}, ${this.map.addBusinessEntityShippingAddress}`,
    );

    if (addBtn) {
      const addressType = addBtn.matches(this.map.addBusinessEntityBillingAddress)
        ? AddressTypeEnum.BILLING
        : AddressTypeEnum.SHIPPING;
      this.addFormToCollection(addBtn, addressType);
      return;
    }

    const deleteBtn = target.closest<HTMLElement>(this.map.deleteBusinessEntityAddress);

    if (deleteBtn) {
      this.removeAddress(deleteBtn);
      return;
    }

    const setAsDefaultBtn = target.closest<HTMLElement>(this.map.setAsDefaultBusinessEntityAddress);

    if (setAsDefaultBtn) {
      this.setAsDefaultAddress(setAsDefaultBtn);
    }
  };

  private initDefaultShippingAddress(): void {
    const defaultElement = document
      .querySelector(this.map.businessEntityDefaultShippingAddress) as HTMLInputElement;
    const defaultValue = defaultElement?.value ?? '1';

    document.querySelectorAll<HTMLElement>(this.map.businessEntityShippingAddress)
      .forEach((input) => {
        const addressCard = input.querySelector<AddressCard>(this.map.businessEntityAddressId);

        if (addressCard === null) {
          return;
        }

        this.addRequiredToggler(addressCard);

        if (addressCard.dataset.addressIndex !== defaultValue) {
          this.addSetAsDefaultBtn(input);
        } else {
          this.selectedShippingAddress = input.querySelector('div.card');
          BusinessEntityAddressCollection.addBgLightToCard(this.selectedShippingAddress);
        }
      });
  }

  private initDefaultBillingAddress(): void {
    const defaultElement = document
      .querySelector(this.map.businessEntityDefaultBillingAddress) as HTMLInputElement;
    const defaultValue = defaultElement?.value ?? '1';

    document.querySelectorAll<HTMLElement>(this.map.businessEntityBillingAddress)
      .forEach((input) => {
        const addressCard = input.querySelector<AddressCard>(this.map.businessEntityAddressId);

        if (addressCard === null) {
          return;
        }

        this.addRequiredToggler(addressCard);

        if (addressCard.dataset.addressIndex !== defaultValue) {
          this.addSetAsDefaultBtn(input);
        } else {
          this.selectedBillingAddress = input.querySelector('div.card');
          BusinessEntityAddressCollection.addBgLightToCard(this.selectedBillingAddress);
        }
      });
  }

  private addRequiredToggler(addressCard: AddressCard): void {
    this.addCountryPostcodeRequiredToggler(addressCard);
    this.addCountryStateSelectionToggler(addressCard);
  }

  private addFormToCollection(addBtn: HTMLElement, addressType: AddressTypeEnum): void {
    const collectionHolder = document.querySelector(`.${addBtn.dataset.collectionHolderClass}`) as HTMLElement;

    const item = document.createElement('li');

    item.innerHTML = collectionHolder
      .dataset
      .prototype!
      .replace(
        /__name__/g,
        collectionHolder.dataset.index!,
      );

    this.addDeleteLink(item);
    const addressCard = item.querySelector('div.card') as AddressCard;

    if (addressType === AddressTypeEnum.BILLING && !this.selectedBillingAddress) {
      this.setDefaultAddress(addressCard, AddressTypeEnum.BILLING);
    } else if (addressType === AddressTypeEnum.SHIPPING && !this.selectedShippingAddress) {
      this.setDefaultAddress(addressCard, AddressTypeEnum.SHIPPING);
    } else {
      this.addSetAsDefaultBtn(item);
    }

    collectionHolder.appendChild(item);

    this.addRequiredToggler(addressCard);

    collectionHolder.dataset.index = String(Number(collectionHolder.dataset.index) + 1);

    this.renumberAddresses(collectionHolder);
  }

  private renumberAddresses(collectionHolder: HTMLElement): void {
    const template = collectionHolder.dataset.addressLabelTemplate ?? '';
    const labels = collectionHolder
      .querySelectorAll<HTMLElement>(`li ${this.map.businessEntityAddressPosition}`);

    for (let index = 0; index < labels.length; index += 1) {
      labels[index].textContent = template.replace('%position%', String(index + 1));
    }
  }

  private addDeleteLink(item: HTMLElement): void {
    const removeFormButton = document.createElement('button');
    removeFormButton.type = 'button';
    removeFormButton.className = `btn btn-link ${this.map.deleteBusinessEntityAddress.slice(1)}`;

    const icon = document.createElement('i');
    icon.className = 'material-icons text-danger';
    icon.textContent = 'delete';

    removeFormButton.appendChild(icon);

    const selector = this.map.deleteBusinessEntityAddressBtnPlaceholder;
    const removeFormButtonLocation = item.querySelector(selector);

    if (removeFormButtonLocation) {
      removeFormButtonLocation.replaceChildren(removeFormButton);
    }
  }

  private removeAddress(deleteBtn: HTMLElement): void {
    const item = deleteBtn.closest('li');

    if (!item) {
      return;
    }

    const collectionHolder = item.parentElement;

    if (item.querySelector(this.map.businessEntityAddressId) === this.selectedShippingAddress) {
      const addressIndex = this.selectedShippingAddress?.dataset.addressIndex;
      const addressCardElementSelector = `${this.map.businessEntityShippingAddress} `
        + `div.card:not([data-address-index="${addressIndex}"])`;
      this.setDefaultAddress(
        document.querySelector(addressCardElementSelector) as AddressCard,
        AddressTypeEnum.SHIPPING,
      );
    }

    if (item.querySelector(this.map.businessEntityAddressId) === this.selectedBillingAddress) {
      const addressIndex = this.selectedBillingAddress?.dataset.addressIndex;
      const addressCardElementSelector = `${this.map.businessEntityBillingAddress} `
        + `div.card:not([data-address-index="${addressIndex}"])`;
      this.setDefaultAddress(
        document.querySelector(addressCardElementSelector) as AddressCard,
        AddressTypeEnum.BILLING,
      );
    }

    item.remove();

    if (collectionHolder) {
      this.renumberAddresses(collectionHolder);
    }
  }

  private addSetAsDefaultBtn(item: HTMLElement): void {
    const setAsDefaultBtn = document.createElement('button');
    setAsDefaultBtn.type = 'button';
    setAsDefaultBtn.className = `btn btn-link ${this.map.setAsDefaultBusinessEntityAddress.slice(1)}`;
    setAsDefaultBtn.textContent = window.translate_javascripts['Set as default'];

    const setAsDefaultBtnLocation = item.querySelector(
      this.map.setAsDefaultBusinessEntityAddressBtnPlaceholder,
    );

    // `item` may be the <li> wrapper or the card itself: target the card to mirror addBgLightToCard.
    const card = item.matches('div.card') ? item : item.querySelector('div.card');
    card?.classList.remove('bg-light');
    card?.querySelector('div.card-header')?.classList.remove('bg-light');

    if (setAsDefaultBtnLocation) {
      setAsDefaultBtnLocation.replaceChildren(setAsDefaultBtn);
    }
  }

  private setAsDefaultAddress(setAsDefaultBtn: HTMLElement): void {
    const addressCard = setAsDefaultBtn.closest(this.map.businessEntityAddressId) as AddressCard;

    if (!addressCard) {
      return;
    }

    const addressType = setAsDefaultBtn.closest(this.map.businessEntityBillingAddressCollection)
      ? AddressTypeEnum.BILLING
      : AddressTypeEnum.SHIPPING;

    this.setDefaultAddress(addressCard, addressType);
  }

  private static addBgLightToCard(element: AddressCard | null): void {
    element?.classList.add('bg-light');
    element?.querySelector('div.card-header')?.classList.add('bg-light');
  }

  private setDefaultAddress(element: AddressCard, addressType: AddressTypeEnum): void {
    let selector: string;

    switch (addressType) {
      case AddressTypeEnum.BILLING:
        selector = this.map.businessEntityDefaultBillingAddress;
        break;
      default:
        selector = this.map.businessEntityDefaultShippingAddress;
    }

    if (element) {
      const defaultBtnPlaceholder = element
        .querySelector(this.map.setAsDefaultBusinessEntityAddressBtnPlaceholder) as HTMLElement;
      defaultBtnPlaceholder.innerHTML = '';
      BusinessEntityAddressCollection.addBgLightToCard(element);
    }

    if (addressType === AddressTypeEnum.BILLING) {
      if (this.selectedBillingAddress) {
        this.addSetAsDefaultBtn(this.selectedBillingAddress);
      }
      this.selectedBillingAddress = element;
    } else if (addressType === AddressTypeEnum.SHIPPING) {
      if (this.selectedShippingAddress) {
        this.addSetAsDefaultBtn(this.selectedShippingAddress);
      }
      this.selectedShippingAddress = element;
    }

    const defaultAddressElement = document.querySelector(selector) as HTMLInputElement;
    defaultAddressElement.value = element?.dataset.addressIndex ?? '0';
  }

  private addCountryPostcodeRequiredToggler(item: AddressCard): void {
    const {addressIndex} = item.dataset;
    const itemSelector = `[data-address-index="${addressIndex}"] `;

    new CountryPostcodeRequiredToggler(
      itemSelector + this.map.businessEntityAddressCountrySelect,
      itemSelector + this.map.businessEntityAddressPostcodeSelect,
      itemSelector + this.map.businessEntityAddressPostcodeLabel,
    );
  }

  private addCountryStateSelectionToggler(item: AddressCard): void {
    const {addressIndex} = item.dataset;
    const itemSelector = `[data-address-index="${addressIndex}"] `;

    new CountryStateSelectionToggler(
      itemSelector + this.map.businessEntityAddressCountrySelect,
      itemSelector + this.map.businessEntityAddressStateSelect,
      itemSelector + this.map.businessEntityAddressStateBlock,
    );
  }
}
