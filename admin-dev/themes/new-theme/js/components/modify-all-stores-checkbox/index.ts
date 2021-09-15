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

import MultiStoreModifyAllMap from './modify-all-stores-checkbox-map';

const {$} = window;

/**
 * Enables multi store modify all checkboxes on the page. The checkboxes are hidden by default,
 * they appear on input focus and stay visible when the value changed.
 */
export default class ModifyAllStoresCheckbox {
  private modifyAllNamePrefix: string;

  /**
   * @param {string} modifyAllNamePrefix
   */
  constructor(modifyAllNamePrefix = '') {
    if (!modifyAllNamePrefix) {
      this.modifyAllNamePrefix = 'modify_all_stores_';
    } else {
      this.modifyAllNamePrefix = modifyAllNamePrefix;
    }

    this.init();
  }

  init(): void {
    const widgets = document.querySelectorAll(MultiStoreModifyAllMap.modifyAllWidgets);
    widgets.forEach((widget: Element) => {
      const widgetCheckbox: HTMLInputElement = <HTMLInputElement>widget.querySelector(MultiStoreModifyAllMap.widgetCheckbox);

      if (widgetCheckbox) {
        // If checkbox is already checked on page load (after submit with errors for example) it is considered updated and visible
        if (widgetCheckbox.checked) {
          widget.classList.add(MultiStoreModifyAllMap.updatedClass);
        }

        const multiStoreFieldId: string = widgetCheckbox.id.replace(this.modifyAllNamePrefix, '');
        const multiStoreField: HTMLInputElement = <HTMLInputElement>document.getElementById(multiStoreFieldId);

        if (multiStoreField) {
          // Toggle element when field is focused/unfocused
          multiStoreField.addEventListener('focus', () => {
            widget.classList.add(MultiStoreModifyAllMap.fieldFocusedClass);
          });
          multiStoreField.addEventListener('blur', () => {
            widget.classList.remove(MultiStoreModifyAllMap.fieldFocusedClass);
          });

          // When the checkbox is hovered keep it visible (it will be hidden when field is unfocused otherwise)
          widget.addEventListener('mouseenter', () => {
            widget.classList.add(MultiStoreModifyAllMap.focusedClass);
          });
          widget.addEventListener('mouseleave', () => {
            widget.classList.remove(MultiStoreModifyAllMap.focusedClass);
          });

          // Once the field (or the checkbox) has changed the checkbox is permanently visible
          multiStoreField.addEventListener('change', () => {
            widget.classList.add(MultiStoreModifyAllMap.updatedClass);
          });
          // We check the event via JQuery as well because some components use internal JQuery event instead of native
          // ones (like select2)
          $(multiStoreField).on('change', () => {
            widget.classList.add(MultiStoreModifyAllMap.updatedClass);
          });
          // Check for checkbox change also, in case it is modified programmatically
          widgetCheckbox.addEventListener('change', () => {
            widget.classList.add(MultiStoreModifyAllMap.updatedClass);
          });
        }
      }
    });
  }
}
