/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import BusinessEntityFormMap from '@pages/business-entity/form/business-entity-form-map';
import CountryPostcodeRequiredToggler from '@components/country-postcode-required-toggler';
import CountryStateSelectionToggler from '@components/country-state-selection-toggler';

const {$} = window;

/* eslint-disable */
enum AddressTypeEnum {
  BILLING = 'billing',
  SHIPPING = 'shipping',
}
/* eslint-enable */

type AddressCard = HTMLElement & {
  dataset: DOMStringMap & {
    addressIndex: string;
  };
};

let selectedBusinessEntityShippingAddress: AddressCard | null = null;
let selectedBusinessEntityBillingAddress: AddressCard | null = null;

const {
  addBusinessEntityBillingAddress,
  addBusinessEntityShippingAddress,
  businessEntityBillingAddress,
  businessEntityShippingAddress,
  businessEntityDefaultShippingAddress,
  businessEntityDefaultBillingAddress,
  businessEntityAddressId,
  setAsDefaultBusinessEntityAddressBtnPlaceholder,
  businessEntityAddressCountrySelect,
  businessEntityAddressPostcodeSelect,
  businessEntityAddressPostcodeLabel,
  businessEntityAddressStateSelect,
  businessEntityAddressStateBlock,
} : {
  [key: string]: string;
} = BusinessEntityFormMap;

$(() => {
  document
    .querySelectorAll<HTMLElement>(addBusinessEntityShippingAddress)
    .forEach((btn) => {
      btn.addEventListener('click', (e) => addFormToCollection(e, AddressTypeEnum.SHIPPING));
    });
  document
    .querySelectorAll<HTMLElement>(addBusinessEntityBillingAddress)
    .forEach((btn) => {
      btn.addEventListener('click', (e) => addFormToCollection(e, AddressTypeEnum.BILLING));
    });

  document.querySelectorAll<HTMLElement>(`${businessEntityBillingAddress}, ${businessEntityShippingAddress}`)
    .forEach((input) => {
      addBusinessEntityAddressFormDeleteLink(input);
    });

  initEventListenerForBusinessEntityDefaultBillingAddress();
  initEventListenerForBusinessEntityDefaultShippingAddress();
});

const initEventListenerForBusinessEntityDefaultShippingAddress = () => {
  const businessEntityDefaultShippingAddressElement = document
    .querySelector(businessEntityDefaultShippingAddress) as HTMLInputElement;
  const businessEntityDefaultShippingAddressValue = businessEntityDefaultShippingAddressElement?.value ?? '1';

  document.querySelectorAll<HTMLElement>(businessEntityShippingAddress)
    .forEach((input) => {
      const addressCard = input.querySelector<AddressCard>(businessEntityAddressId);

      if (addressCard === null) {
        return;
      }

      addRequiredToggler(addressCard);

      if (addressCard?.dataset.addressIndex !== businessEntityDefaultShippingAddressValue) {
        addSetAsDefaultBusinessEntityAddressBtn(input, AddressTypeEnum.SHIPPING);
      } else {
        selectedBusinessEntityShippingAddress = input.querySelector('div.card');
        addBgLightToCard(selectedBusinessEntityShippingAddress);
      }
    });
};

const initEventListenerForBusinessEntityDefaultBillingAddress = () => {
  const businessEntityDefaultBillingAddressElement = document
    .querySelector(businessEntityDefaultBillingAddress) as HTMLInputElement;
  const businessEntityDefaultBillingAddressValue = businessEntityDefaultBillingAddressElement?.value ?? '1';

  document.querySelectorAll<HTMLElement>(businessEntityBillingAddress)
    .forEach((input) => {
      const addressCard = input.querySelector<AddressCard>(businessEntityAddressId);

      if (addressCard === null) {
        return;
      }

      addRequiredToggler(addressCard);

      if (addressCard?.dataset.addressIndex !== businessEntityDefaultBillingAddressValue) {
        addSetAsDefaultBusinessEntityAddressBtn(input, AddressTypeEnum.BILLING);
      } else {
        selectedBusinessEntityBillingAddress = input.querySelector('div.card');
        addBgLightToCard(selectedBusinessEntityBillingAddress);
      }
    });
};

function addRequiredToggler(addressCard: HTMLElement & { dataset: DOMStringMap & { addressIndex: string } }) {
  addCountryPostcodeRequiredToggler(addressCard);
  addCountryStateSelectionToggler(addressCard);
}

function addFormToCollection(e: Event, addressType: AddressTypeEnum): void {
  const target = e.currentTarget as HTMLElement;
  const collectionHolder = document.querySelector(`.${target.dataset.collectionHolderClass}`) as HTMLElement;

  const item = document.createElement('li');

  item.innerHTML = collectionHolder
    .dataset
    .prototype!
    .replace(
      /__name__/g,
      collectionHolder.dataset.index!,
    );

  addBusinessEntityAddressFormDeleteLink(item);
  const addressCard = item.querySelector('div.card') as AddressCard;

  if (addressType === AddressTypeEnum.BILLING && !selectedBusinessEntityBillingAddress) {
    setDefaultBusinessEntityAddress(addressCard, AddressTypeEnum.BILLING);
  } else if (addressType === AddressTypeEnum.SHIPPING && !selectedBusinessEntityShippingAddress) {
    setDefaultBusinessEntityAddress(addressCard, AddressTypeEnum.SHIPPING);
  } else {
    addSetAsDefaultBusinessEntityAddressBtn(item, addressType);
  }

  collectionHolder.appendChild(item);

  addRequiredToggler(addressCard);

  $(item).find('[data-toggle="select2"]').select2({
    theme: 'bootstrap4',
  });

  collectionHolder.dataset.index = String(Number(collectionHolder.dataset.index) + 1);
}

function addBusinessEntityAddressFormDeleteLink(item: HTMLElement) {
  const removeFormButton = document.createElement('button');
  removeFormButton.type = 'button';
  removeFormButton.className = 'btn btn-link delete-business-entity-address';

  const icon = document.createElement('i');
  icon.className = 'material-icons text-danger';
  icon.textContent = 'delete';

  removeFormButton.appendChild(icon);

  const selector = BusinessEntityFormMap.deleteBusinessEntityAddressBtnPlaceholder;
  const removeFormButtonLocation = item.querySelector(selector);

  if (removeFormButtonLocation) {
    removeFormButtonLocation.replaceChildren(removeFormButton);
  }

  removeFormButton.addEventListener('click', () => {
    if (item.querySelector(businessEntityAddressId) === selectedBusinessEntityShippingAddress) {
      const adddressCardElementSelector = `${BusinessEntityFormMap.businessEntityShippingAddress} div.card`;
      setDefaultBusinessEntityAddress(
        document.querySelector(adddressCardElementSelector) as AddressCard,
        AddressTypeEnum.SHIPPING,
      );
    }

    if (item.querySelector(businessEntityAddressId) === selectedBusinessEntityBillingAddress) {
      const addressIndex = selectedBusinessEntityBillingAddress?.dataset.addressIndex;
      const addressCardElementSelector = `${businessEntityBillingAddress} div:not([data-address-index="${addressIndex}"])`;
      setDefaultBusinessEntityAddress(
        document
          .querySelector(addressCardElementSelector) as AddressCard,
        AddressTypeEnum.BILLING,
      );
    }
    item.remove();
  });
}

function addSetAsDefaultBusinessEntityAddressBtn(item: HTMLElement, addressType: AddressTypeEnum) {
  const setAsDefaultBusinessEntityAddressBtn = document.createElement('button');
  setAsDefaultBusinessEntityAddressBtn.type = 'button';
  setAsDefaultBusinessEntityAddressBtn.className = 'btn btn-link set-as-default-business-entity-address';
  setAsDefaultBusinessEntityAddressBtn.textContent = window.translate_javascripts['Set as default'];

  const setAsDefaultBusinessEntityAddressBtnLocation = item.querySelector(
    setAsDefaultBusinessEntityAddressBtnPlaceholder,
  );

  item.classList.remove('bg-light');
  item.querySelector('div.card-header')?.classList.remove('bg-light');

  if (setAsDefaultBusinessEntityAddressBtnLocation) {
    setAsDefaultBusinessEntityAddressBtnLocation.replaceChildren(setAsDefaultBusinessEntityAddressBtn);
    setAsDefaultBusinessEntityAddressBtn.addEventListener(
      'click',
      (e) => setAsDefaultBusinessEntityAddress(e, addressType),
    );
  }
}

function setAsDefaultBusinessEntityAddress(e: Event, addressType: AddressTypeEnum) {
  const target = e.currentTarget as HTMLElement;
  const addressCard = target.closest(businessEntityAddressId) as AddressCard;

  if (!addressCard) {
    return;
  }

  setDefaultBusinessEntityAddress(addressCard, addressType);
}

function addBgLightToCard(element: AddressCard|null) {
  // eslint-disable-next-line no-param-reassign
  element?.classList.add('bg-light');
  // eslint-disable-next-line no-param-reassign
  (element as Element)?.querySelector('div.card-header')?.classList.add('bg-light');
}

const setDefaultBusinessEntityAddress = (element: AddressCard, addressType: AddressTypeEnum) => {
  let selector: string;

  switch (addressType) {
    case AddressTypeEnum.BILLING:
      selector = businessEntityDefaultBillingAddress;
      break;
    default:
      selector = businessEntityDefaultShippingAddress;
  }

  if (element) {
    // eslint-disable-next-line no-param-reassign
    ((element as Element).querySelector(setAsDefaultBusinessEntityAddressBtnPlaceholder) as HTMLElement)
      .innerHTML = '';
    addBgLightToCard(element);
  }

  if (addressType === AddressTypeEnum.BILLING) {
    if (selectedBusinessEntityBillingAddress) {
      addSetAsDefaultBusinessEntityAddressBtn(selectedBusinessEntityBillingAddress as HTMLElement, addressType);
    }
    selectedBusinessEntityBillingAddress = element;
  } else if (addressType === AddressTypeEnum.SHIPPING) {
    if (selectedBusinessEntityShippingAddress) {
      addSetAsDefaultBusinessEntityAddressBtn(selectedBusinessEntityShippingAddress as HTMLElement, addressType);
    }
    selectedBusinessEntityShippingAddress = element;
  }

  const defaultAddressElement = document.querySelector(selector) as HTMLInputElement;
  defaultAddressElement.value = element?.dataset.addressIndex ?? '0';
};

const addCountryPostcodeRequiredToggler = (item: AddressCard) => {
  const {addressIndex} = item.dataset;
  const itemSelector = `[data-address-index="${addressIndex}"] `;

  new CountryPostcodeRequiredToggler(
    itemSelector + businessEntityAddressCountrySelect,
    itemSelector + businessEntityAddressPostcodeSelect,
    itemSelector + businessEntityAddressPostcodeLabel,
  );
};

const addCountryStateSelectionToggler = (item: AddressCard) => {
  const {addressIndex} = item.dataset;
  const itemSelector = `[data-address-index="${addressIndex}"] `;

  new CountryStateSelectionToggler(
    itemSelector + businessEntityAddressCountrySelect,
    itemSelector + businessEntityAddressStateSelect,
    itemSelector + businessEntityAddressStateBlock,
  );
};
