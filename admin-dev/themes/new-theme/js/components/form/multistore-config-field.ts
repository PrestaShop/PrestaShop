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

import ComponentsMap from '@components/components-map';
import ContextualNotification from '@components/contextual-notification';

const {$} = window;

export default class MultistoreConfigField {
  constructor() {
    this.updateMultistoreFieldOnChange();
    this.initContextualNotification();
  }

  updateMultistoreFieldOnChange(): void {
    $(document).on('change', ComponentsMap.multistoreCheckbox, function () {
      const input = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.inputNotCheckbox);
      const inputContainer = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.inputContainer);
      const labelContainer = $(this)
        .closest(ComponentsMap.formGroup)
        .find(ComponentsMap.formControlLabel);
      const isChecked = $(this).is(':checked');
      inputContainer.toggleClass('disabled', !isChecked);
      labelContainer.toggleClass('disabled', !isChecked);
      input.prop('disabled', !isChecked);
    });
  }

  initContextualNotification(): void {
    const configKeyShopPrefix = 'multistore-checkbox-shop-';
    const configKeyGroupPrefix = 'multistore-checkbox-group-';
    const multistoreHeader = document.querySelector('.header-multishop');

    if (multistoreHeader === null || !(multistoreHeader instanceof HTMLElement) || !multistoreHeader.dataset.checkboxNotification) {
      return;
    }

    const contextualNotification = new ContextualNotification();
    let notificationKey = configKeyGroupPrefix + multistoreHeader.dataset.groupId;

    if (multistoreHeader.hasAttribute('data-shop-id')) {
      notificationKey = configKeyShopPrefix + multistoreHeader.dataset.shopId;
    }

    // check if key exists, if yes: display or not depending on given value
    const configValue = contextualNotification.getItem(notificationKey);

    if (configValue === true || configValue === null) {
      contextualNotification.displayNotification(multistoreHeader.dataset.checkboxNotification, notificationKey);
    }

    // if the config doesn't exist, we set it to true
    if (configValue === null) {
      contextualNotification.setItem(notificationKey, true);
    }
  }
}
