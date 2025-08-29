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
 * This component toggles forms children based on a radio inputs.
 */
export default class ToggleChildrenChoice {
  private readonly toggleChildrenChoice: string;

  private readonly childrenSelect: string;

  private readonly childrenRadios: string;

  private readonly toggleChild: string;

  private readonly selectedChild: (childName: string) => string;

  constructor(options: Record<string, any> = {}) {
    const opts = options || {};

    this.toggleChildrenChoice = opts.toggleChildrenChoice || '.toggle-children-choice';
    this.childrenSelect = opts.childrenSelect || ':scope > .form-group > .toggle-children-choice-select';
    this.childrenRadios = `${this.childrenSelect} input[type="radio"]`;
    this.toggleChild = opts.toggleChild || ':scope > .toggle-children-choice-container > .toggle-children-choice-child';
    this.selectedChild = (childName: string) => `${this.toggleChild}[data-toggle-name=${childName}]`;

    this.init();
  }

  private init(): void {
    document.querySelectorAll<HTMLElement>(this.toggleChildrenChoice).forEach((toggleChildrenChoice: HTMLElement) => {
      toggleChildrenChoice.querySelectorAll<HTMLInputElement>(this.childrenRadios).forEach((radio: HTMLInputElement) => {
        radio.addEventListener('change', () => {
          toggleChildrenChoice.querySelectorAll<HTMLElement>(this.toggleChild).forEach((formChild: HTMLElement) => {
            formChild.classList.add('d-none');
          });

          // Value can be empty when a placeholder has been set
          if (radio.value) {
            const selectedChild = toggleChildrenChoice.querySelector<HTMLElement>(this.selectedChild(radio.value));

            if (selectedChild) {
              selectedChild.classList.remove('d-none');
            }
          }
          const {eventEmitter} = window.prestashop.instance;
          eventEmitter.emit('ToggleChildrenChoice:toggled', radio);
        });
      });
    });
  }
}
