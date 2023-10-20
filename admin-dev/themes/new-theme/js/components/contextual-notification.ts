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

import ComponentsMap from '@components/components-map';

/**
 * This class is responsible for initiating, setting and getting data related to contextual notifications,
 * that is to say: should we display the notification related to this key identifier in local storage.
 * It also displays the notification itself
 */
class ContextualNotification {
  // all contextual notification data will be stored under this key in local storage
  private localStorageKey = 'contextual_notifications';

  constructor() {
    $(document).on(
      'click',
      ComponentsMap.contextualNotification.close,
      (Event) => this.disableNotification(Event),
    );
  }

  setItem(key: any, value: boolean): void {
    const notificationList = JSON.parse(this.getNotificationList());
    notificationList[key] = value;

    localStorage.setItem(this.localStorageKey, JSON.stringify(notificationList));
  }

  getItem(key: any): boolean|null {
    const notificationList = JSON.parse(this.getNotificationList());

    if (key in notificationList) {
      return notificationList[key];
    }

    return null;
  }

  displayNotification(message: string, key: string): void {
    const $element = document.createElement('div');
    $element.classList.add('alert', 'alert-info', ComponentsMap.contextualNotification.notificationClass);
    $element.setAttribute('data-notification-key', key);
    $element.innerHTML = `${message}<button type="button" class="close" data-dismiss="alert">&times;</button>`;

    const notificationBoxId = document.getElementById(ComponentsMap.contextualNotification.notificationBoxId);

    if (notificationBoxId instanceof HTMLElement) {
      notificationBoxId.append($element);
      return;
    }

    const contentMessageBox = document.getElementById(ComponentsMap.contextualNotification.messageBoxId);

    if (contentMessageBox instanceof HTMLElement) {
      contentMessageBox.append($element);
    }
  }

  private disableNotification(event: any): void {
    const notificationKey = $(event.target).parent().attr('data-notification-key');

    if (notificationKey !== '') {
      this.setItem(notificationKey, false);
    }
  }

  private getNotificationList(): string {
    return localStorage.getItem(this.localStorageKey) ?? '{}';
  }
}

/**
 * Initializes contextual notification on the multistore header
 * Example:
 *     initContextualNotification('checkbox');
 *
 * @param {string} key Key of the contextual notification
 */
export default function initContextualNotification(key: string): void {
  const multistoreHeader = document.querySelector(ComponentsMap.multistoreHeader.headerMultiShop);
  const dataAttr = `data-${key}-notification`;

  // Only search notification message for "single shop" or "shop group" context since no notification is needed for "All shops" context
  if (multistoreHeader === null
    || !(multistoreHeader instanceof HTMLElement)
    || !multistoreHeader.hasAttribute(dataAttr)
    || (multistoreHeader.dataset.shopId === undefined && multistoreHeader.dataset.groupId === undefined)) {
    return;
  }

  // make localstorage key for this context
  const contextualNotification = new ContextualNotification();

  const notificationKey = multistoreHeader.dataset.shopId !== undefined
    ? `${key}-shop-${multistoreHeader.dataset.shopId}`
    : `${key}-group-${multistoreHeader.dataset.groupId}`;

  // check if key exists, if yes: display or not depending on given value
  const configValue = contextualNotification.getItem(notificationKey);

  const message = multistoreHeader.getAttribute(dataAttr);

  if ((configValue === true || configValue === null) && message !== null) {
    contextualNotification.displayNotification(message, notificationKey);
  }

  // if the config doesn't exist, we set it to true
  if (configValue === null) {
    contextualNotification.setItem(notificationKey, true);
  }
}
