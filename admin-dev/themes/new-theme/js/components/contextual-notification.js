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

/**
 * This class is responsible for initiating, setting and getting data related to contextual notifications,
 * that is to say: should we display the notification related to this key identifier in local storage.
 * It also displays the notification itself
 */
export default class ContextualNotification {
  constructor() {
    // all contextual notification data will be stored under this key in local storage
    this.localStorageKey = 'contextual_notifications';

    let notificationList = localStorage.getItem(this.localStorageKey);

    // if the contextual_notifications key doesn't exist in localstorage, we set it as empty
    if (notificationList === null) {
      notificationList = {};
      localStorage.setItem(this.localStorageKey, JSON.stringify(notificationList));
    }

    $(document).on('click', '.contextual-notification .close', (Event) => this.disableNotification(Event));
  }

  setItem(key, value) {
    let notificationList = localStorage.getItem(this.localStorageKey);
    notificationList = JSON.parse(notificationList);

    notificationList[key] = value;

    localStorage.setItem(this.localStorageKey, JSON.stringify(notificationList));
  }

  getItem(key) {
    const notificationList = JSON.parse(localStorage.getItem(this.localStorageKey));

    if (key in notificationList) {
      return notificationList[key];
    }

    return null;
  }

  disableNotification(event) {
    const notificationKey = $(event.target).parent().attr('data-notification-key');

    if (notificationKey !== '') {
      this.setItem(notificationKey, false);
    }
  }

  displayNotification(message, key) {
    const $element = document.createElement('div');
    $element.classList.add('alert', 'alert-info', 'contextual-notification');
    $element.setAttribute('data-notification-key', key);
    $element.innerHTML = `${message}<button type="button" class="close" data-dismiss="alert">&times;</button>`;

    const ajaxConfirmation = document.getElementById('ajax_confirmation');
    ajaxConfirmation.parentNode.insertBefore($element, ajaxConfirmation);
  }
}
