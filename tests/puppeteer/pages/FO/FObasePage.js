const CommonPage = require('../commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.desktopLogo = '#_desktop_logo';
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
    this.userInfoLink = '#_desktop_user_info';
    this.logoutLink = `${this.userInfoLink} .user-info a.logout`;
    this.contactLink = '#contact-link';
    this.categoryMenu = '#category-%ID > a';
  }

  /**
   * go to the home page
   */
  async goToHomePage() {
    await this.waitForSelectorAndClick(this.desktopLogo);
  }

  /**
   * Filter by category
   * @param categoryID, category id from the BO
   */
  async filterByCategory(categoryID) {
    await this.waitForSelectorAndClick(this.categoryMenu.replace('%ID', categoryID));
  }

  /**
   * Filter by subcategory
   * @param categoryID, category id from the BO
   * @param subCategoryID, subcategory id from the BO
   */
  async filterSubCategory(categoryID, subCategoryID) {
    await this.page.hover(this.categoryMenu.replace('%ID', categoryID));
    await this.waitForSelectorAndClick(this.categoryMenu.replace('%ID', subCategoryID));
  }

  /**
   * Go to login Page
   * @return {Promise<void>}
   */
  async goToLoginPage() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.userInfoLink),
    ]);
  }

  /**
   * Check if customer is connected
   * @return {Promise<boolean|true>}
   */
  async isCustomerConnected() {
    return this.elementVisible(this.logoutLink, 1000);
  }
};
