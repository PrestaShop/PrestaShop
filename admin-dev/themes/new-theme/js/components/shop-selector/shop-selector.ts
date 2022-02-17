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
import Modal from '@components/modal/modal';
import ComponentsMap from '@components/components-map';

import ClickEvent = JQuery.ClickEvent;

export default class ShopSelector {
  private modalContent: Element | null;

  constructor() {
    this.modalContent = document.querySelector(ComponentsMap.shopSelector.modalContent);
  }

  isAvailable(): boolean {
    return this.modalContent !== null;
  }

  async show(modalTitle: string, selectedShopCallback: (shopId: number) => void): Promise<void> {
    const modal: Modal = new Modal({
      id: ComponentsMap.shopSelector.modalId,
      modalTitle,
      closable: true,
    });

    modal.render(<string> this.modalContent?.outerHTML);
    modal.show();

    $(`#${ComponentsMap.shopSelector.modalId}`).on('click', ComponentsMap.shopSelector.shopItem, (event: ClickEvent) => {
      modal.hide();
      selectedShopCallback(parseInt(event.currentTarget.dataset.shopId, 10));
    });
  }
}
