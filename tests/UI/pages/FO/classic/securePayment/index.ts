import FOBasePage from '@pages/FO/FObasePage';

/**
 * Secure payment page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SecurePaymentPage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on secure payment page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Secure payment';
  }
}

const securePaymentPage = new SecurePaymentPage();
export {securePaymentPage, SecurePaymentPage};
