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
 * Shows or hides specified element based on specified checkbox "checked" property.
 */
export default class VisibilityToggler {
  checkboxesSelector: string;

  elementToToggleSelector: string;

  checkedValueForHiding: string;

  constructor(
    checkboxesSelector: string,
    checkedValueForHiding: string,
    elementToToggleSelector: string,
  ) {
    this.checkboxesSelector = checkboxesSelector;
    this.elementToToggleSelector = elementToToggleSelector;
    this.checkedValueForHiding = checkedValueForHiding;
    this.init();
  }

  private init(): void {
    const checkboxes: NodeListOf<HTMLInputElement> = document.querySelectorAll(this.checkboxesSelector);
    const initiallyCheckedCheckbox: HTMLInputElement|null = document.querySelector(`${this.checkboxesSelector}:checked`);
    this.toggle(initiallyCheckedCheckbox?.value === this.checkedValueForHiding);

    checkboxes.forEach((checkbox: HTMLInputElement) => {
      checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
          this.toggle(checkbox.value === this.checkedValueForHiding);
        }
      });
    });
  }

  private toggle(hide: boolean): void {
    const elementToToggle = document.querySelector(this.elementToToggleSelector) as HTMLElement;
    elementToToggle.classList.toggle('d-none', hide);
  }
}
