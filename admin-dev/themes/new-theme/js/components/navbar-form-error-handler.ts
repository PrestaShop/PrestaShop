/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
  private readonly form: HTMLElement;

  private readonly navbarHandler: NavbarHandler;

  constructor(options: NavbarFormErrorHandlerType) {
    this.navbarHandler = options.navbarHandler;
    this.form = options.form;

    this.initListener();
  }

  private findAllFormFields(): NodeListOf<HTMLElement> {
    return this.form.querySelectorAll('input, select, textarea');
  }

  private initListener(): void {
    let isFirstInvalidField = false;

    this.findAllFormFields().forEach((field) => {
      field.addEventListener('invalid', () => {
        if (isFirstInvalidField) {
          return;
        }

        isFirstInvalidField = true;

        const tab = field.closest('[role="tabpanel"]');

        if (!tab || typeof tab === null) {
          throw new Error('NavbarFormErrorHandler: Cannot find the tab that contains some form fields in error.');
        }

        if (!('id' in tab)) {
          throw new Error('NavbarFormErrorHandler: Id missing from the tab.');
        }

        this.navbarHandler.switchToTarget(`#${tab.id}`);

        field.scrollIntoView({
          behavior: 'smooth',
          block: 'end',
        });

        // Set a timeout to reset the flag after the current event loop
        setTimeout(() => {
          isFirstInvalidField = false;
        }, 0);
      });
    });
  }
}
