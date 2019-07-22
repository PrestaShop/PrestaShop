const CommonPage = require('../commonPage');

module.exports = class BO_COMMONPAGE extends CommonPage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Dashboard •';

    //top navbar
    this.headerLogoImage = '#header_logo';
    this.userProfileIcon = '#employee_infos';
    this.userProfileLogoutLink = 'a#header_logout';

    //left navbar
    // SELL
    this.ordersParentLink = 'li#subtab-AdminParentOrders>a';
    this.ordersLink = '#subtab-AdminOrders>a';

    this.productsParentLink = 'li#subtab-AdminCatalog>a';
    this.productsLink = '#subtab-AdminProducts>a';

    this.customersParentLink = 'li#subtab-AdminParentCustomer>a';
    this.customersLink = '#subtab-AdminCustomers>a';
  }

  /*
  Methods
   */
  /**
   * Open a subMenu if closed and click on a sublink
   * @param blockSelector
   * @param parentSelector
   * @param linkSelector
   * @returns {Promise<void>}
   */
  async goToSubMenu(parentSelector, linkSelector) {
    if (this.page.elementVisible(linkSelector)) {
        await this.page.click(linkSelector);
      } else {
        //open the block
        await this.page.click(parentSelector);
        await this.page.waitForSelector(linkSelector);
        await this.page.click(linkSelector);
      }
  }

  /**
   * Returns to the dashboard then logout
   * @returns {Promise<void>}
   */
  async logoutBO() {
    await this.page.click(this.headerLogoImage);
    await this.page.waitForSelector(this.userProfileIcon);
    await this.page.click(this.userProfileIcon);
    await this.page.waitForSelector(this.userProfileLogoutLink);
    await this.page.click(this.userProfileLogoutLink);
  }

  /**
   * Close the onboarding modal if exists
   * @returns {Promise<void>}
   */
  async closeOnboardingModal() {
    //await
  }
};
