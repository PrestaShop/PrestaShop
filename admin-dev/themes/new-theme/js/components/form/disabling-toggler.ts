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
export default class DisablingToggler {
  inputForMatchingSelector: string;

  elementToToggleSelector: string;

  matchingValue: string;

  disableOnMatch: boolean;

  constructor(
    inputForMatchingSelector: string,
    matchingValue: string,
    elementToToggleSelector: string,
    disableOnMatch: boolean = true,
  ) {
    this.inputForMatchingSelector = inputForMatchingSelector;
    this.elementToToggleSelector = elementToToggleSelector;
    this.matchingValue = matchingValue;
    this.disableOnMatch = disableOnMatch;
    this.init();
  }

  private init(): void {
    const disablingInputs: NodeListOf<HTMLInputElement> = document.querySelectorAll(this.inputForMatchingSelector);
    const initiallyCheckedCheckbox: HTMLInputElement|null = document.querySelector(`${this.inputForMatchingSelector}:checked`);
    this.toggle(initiallyCheckedCheckbox?.value === this.matchingValue);

    disablingInputs.forEach((input: HTMLInputElement) => {
      input.addEventListener('change', () => {
        this.toggle(input.value === this.matchingValue && this.disableOnMatch);
      });
    });
  }

  private toggle(disable: boolean): void {
    const elementToToggle = document.querySelector(this.elementToToggleSelector) as HTMLElement;
    elementToToggle.classList.toggle('disabled', disable);
    elementToToggle.toggleAttribute('disabled', disable);

    const formElements = elementToToggle.querySelectorAll('input, select, textarea, button, option, fieldset');

    if (formElements.length === 0) {
      return;
    }

    formElements.forEach((element: Element) => {
      element.toggleAttribute('disabled', disable);
    });
  }
}
