import FOBasePage from '@pages/FO/FObasePage';

/**
 * Terms and conditions of use page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class TermsAndConditionsOfUse extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on terms and conditions of use page
   */
  constructor() {
    super();

    this.pageTitle = 'Terms and conditions of use';
  }
}

export default new TermsAndConditionsOfUse();
