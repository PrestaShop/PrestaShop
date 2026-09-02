/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {Modal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import {isUndefined} from '@components/typeguard';

export default function selectShopForEdition(editButton: HTMLElement, shopIds: string[]): void {
  if (isUndefined(editButton.dataset.modalTitle) || isUndefined(editButton.dataset.shopSelector)) {
    return;
  }

  const modal = new Modal({
    id: 'select-shop-for-edition-modal',
    modalTitle: editButton.dataset.modalTitle,
    closable: true,
  });
  modal.render(editButton.dataset.shopSelector);
  // Init shop selector actions
  modal.modal.container.querySelectorAll<HTMLElement>(`.${ProductMap.shops.shopItemClass}`).forEach((selector: HTMLElement) => {
    if (isUndefined(selector.dataset.shopId)) {
      return;
    }

    // Hide shop that are not associated to the product
    if (shopIds.indexOf(selector.dataset.shopId) === -1) {
      selector.classList.add('d-none');
    } else {
      selector.addEventListener('click', () => {
        document.location.href = `${editButton.getAttribute('href')}&setShopContext=s-${selector.dataset.shopId}`;
      });
    }
  });

  // Hide group without shops
  let lastGroup: HTMLElement | null = null;
  let allShopsHidden = true;
  const shopItems = modal.modal.container.querySelectorAll<HTMLElement>(ProductMap.shops.selectorItem);
  shopItems.forEach((selector: HTMLElement, index) => {
    if (selector.classList.contains(ProductMap.shops.groupShopItemClass)) {
      // Hide previous group if all its shops are hidden
      if (lastGroup && allShopsHidden) {
        lastGroup.classList.add('d-none');
      }
      allShopsHidden = true;
      lastGroup = selector;
    } else if (selector.classList.contains(ProductMap.shops.shopItemClass) && !selector.classList.contains('d-none')) {
      allShopsHidden = false;
    }

    // Hide last group if all its shops are hidden
    if (index === shopItems.length - 1 && lastGroup && allShopsHidden) {
      lastGroup.classList.add('d-none');
    }
  });

  // Finally show the modal
  modal.show();
}
