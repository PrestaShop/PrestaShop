/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
