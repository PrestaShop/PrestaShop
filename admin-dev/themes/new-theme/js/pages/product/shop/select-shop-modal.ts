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
import {Modal} from '@components/modal';
import ProductMap from '@pages/product/product-map';
import {isUndefined} from '@PSTypes/typeguard';

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
