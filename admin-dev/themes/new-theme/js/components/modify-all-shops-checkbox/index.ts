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

import MultiShopModifyAllMap from './component-map';

const {$} = window;

/**
 * Enables multi shop modify all checkboxes on the page. The checkboxes are hidden by default,
 * they appear on input focus and stay visible when the value changed.
 */
export default class ModifyAllShopsCheckbox {
  private modifyAllNamePrefix: string;

  /**
   * @param {string} modifyAllNamePrefix
   */
  constructor(modifyAllNamePrefix = '') {
    if (!modifyAllNamePrefix) {
      this.modifyAllNamePrefix = 'modify_all_shops_';
    } else {
      this.modifyAllNamePrefix = modifyAllNamePrefix;
    }

    this.init();
  }

  init(): void {
    const widgets = document.querySelectorAll(MultiShopModifyAllMap.modifyAllWidgets);
    widgets.forEach((widget: Element) => {
      const widgetCheckbox: HTMLInputElement = <HTMLInputElement>widget.querySelector(MultiShopModifyAllMap.widgetCheckbox);

      if (widgetCheckbox) {
        // If checkbox is already checked on page load (after submit with errors for example) it is considered updated and visible
        if (widgetCheckbox.checked) {
          widget.classList.add(MultiShopModifyAllMap.updatedClass);
        }

        const multiShopFieldId: string = widgetCheckbox.id.replace(this.modifyAllNamePrefix, '');
        const multiShopField: HTMLInputElement = <HTMLInputElement>document.getElementById(multiShopFieldId);

        if (multiShopField) {
          // Toggle element when field is focused/unfocused
          multiShopField.addEventListener('focus', () => {
            widget.classList.add(MultiShopModifyAllMap.fieldFocusedClass);
          });
          multiShopField.addEventListener('blur', () => {
            widget.classList.remove(MultiShopModifyAllMap.fieldFocusedClass);
          });

          // When the checkbox is hovered keep it visible (it will be hidden when field is unfocused otherwise)
          widget.addEventListener('mouseenter', () => {
            widget.classList.add(MultiShopModifyAllMap.focusedClass);
          });
          widget.addEventListener('mouseleave', () => {
            widget.classList.remove(MultiShopModifyAllMap.focusedClass);
          });

          // Once the field (or the checkbox) has changed the checkbox is permanently visible
          multiShopField.addEventListener('change', () => {
            widget.classList.add(MultiShopModifyAllMap.updatedClass);
          });
          // We check the event via JQuery as well because some components use internal JQuery event instead of native
          // ones (like select2)
          $(multiShopField).on('change', () => {
            widget.classList.add(MultiShopModifyAllMap.updatedClass);
          });
          // Check for checkbox change also, in case it is modified programmatically
          widgetCheckbox.addEventListener('change', () => {
            widget.classList.add(MultiShopModifyAllMap.updatedClass);
          });
        }
      }
    });
  }
}
