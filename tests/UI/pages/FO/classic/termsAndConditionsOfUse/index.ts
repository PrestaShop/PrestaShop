import FOBasePage from '@pages/FO/FObasePage';

/**
 * Terms and conditions of use page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class TermsAndConditionsOfUsePage extends FOBasePage {
  public readonly pageTitle: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on terms and conditions of use page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Terms and conditions of use';
  }
}

const termsAndConditionsOfUsePage = new TermsAndConditionsOfUsePage();
export {termsAndConditionsOfUsePage, TermsAndConditionsOfUsePage};
