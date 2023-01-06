import FOBasePage from '@pages/FO/FObasePage';

/**
 * Secure payment page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class SecurePayment extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on secure payment page
   */
  constructor() {
    super();

    this.pageTitle = 'Secure payment';
  }
}

export default new SecurePayment();
