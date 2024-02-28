import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * My account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class MyAccountPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly resetPasswordSuccessMessage: string;

  private readonly accountInformationLink: string;

  private readonly accountHistoryLink: string;

  private readonly accountAddressesLink: string;

  private readonly accountFirstAddressLink: string;

  private readonly accountVouchersLink: string;

  private readonly merchandiseReturnsLink: string;

  protected orderSlipsLink: string;

  private readonly successMessageAlert: string;

  protected logoutFooterLink: string;

  private readonly psgdprLink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on my account page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'My account';
    this.resetPasswordSuccessMessage = 'Your password has been successfully reset and a confirmation has been sent to'
      + ' your email address:';

    // Selectors
    this.accountInformationLink = '#identity-link';
    this.accountHistoryLink = '#history-link';
    this.accountAddressesLink = '#addresses-link';
    this.accountFirstAddressLink = '#address-link';
    this.accountVouchersLink = '#discounts-link';
    this.merchandiseReturnsLink = '#returns-link';
    this.orderSlipsLink = '#order-slips-link';
    this.successMessageAlert = '#notifications article.alert-success';
    this.logoutFooterLink = '#main footer a[href*="mylogout"]';
    this.psgdprLink = '#psgdpr-link';
  }

  /*
  Methods
   */
  /**
   * Get success message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getSuccessMessageAlert(page: Page): Promise<string> {
    return this.getTextContent(page, this.successMessageAlert);
  }

  /**
   * Go to account information page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToInformationPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.accountInformationLink);
  }

  /**
   * Go to account credit slips page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToCreditSlipsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.orderSlipsLink);
  }

  /**
   * Go to order history page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToHistoryAndDetailsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.accountHistoryLink);
  }

  /**
   * Is add first address link visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAddFirstAddressLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.accountFirstAddressLink);
  }

  /**
   * Go to addresses page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddressesPage(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.accountFirstAddressLink, 2000)) {
      await this.clickAndWaitForURL(page, this.accountFirstAddressLink);
    } else {
      await this.clickAndWaitForURL(page, this.accountAddressesLink);
    }
  }

  /**
   * Go to vouchers page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToVouchersPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.accountVouchersLink);
  }

  /**
   * Go to merchandise returns page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMerchandiseReturnsPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.merchandiseReturnsLink);
  }

  /**
   * Logout from FO
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async logout(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.logoutFooterLink);
  }

  /**
   * Go to my GDPR personal data page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMyGDPRPersonalDataPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.psgdprLink);
  }
}

const myAccountPage = new MyAccountPage();
export {myAccountPage, MyAccountPage};
