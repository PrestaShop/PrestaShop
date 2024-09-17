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

import NavbarHandler from './navbar-handler';

type NavbarFormErrorHandlerType = {
  form: HTMLElement;
  navbarHandler: NavbarHandler;
}

/**
 * This component is used as a wrapper for the NavbahHandler component. It allows handling
 * tab redirection to the tab that contains some HTML form errors.
 * Use this component only if you are using a form with NavbarHandler.
 */
export default class NavbarFormErrorHandler {
  form: HTMLElement;

  navbarHandler: NavbarHandler;

  firstInvalidField: Element | null = null;

  constructor(options: NavbarFormErrorHandlerType) {
    this.navbarHandler = options.navbarHandler;
    this.form = options.form;

    this.initListener();
    this.resetInvalidField();
  }

  private findRequiredFieldsFromForm(): NodeListOf<Element> {
    return this.form.querySelectorAll('[required]');
  }

  private initListener(): void {
    this.findRequiredFieldsFromForm().forEach((field) => {
      field.addEventListener('invalid', () => {
        if (!this.firstInvalidField) {
          this.firstInvalidField = field;

          const tab = field.closest('[role="tabpanel"]');
          this.navbarHandler.switchToTarget(`#${tab?.id}`);

          field.scrollIntoView({
            behavior: 'smooth',
            block: 'end',
          });
        }
      });
    });
  }

  private resetInvalidField() {
    this.form.addEventListener('click', () => {
      this.firstInvalidField = null;
    }, true);
  }
}
