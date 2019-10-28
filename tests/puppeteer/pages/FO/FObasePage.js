require('module-alias/register');
const CommonPage = require('@pages/commonPage');

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
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorMenuItemLink = `${this.languageSelectorDiv} ul li a[data-iso-code='%LANG']`;

    // footer
    this.siteMapLink = '#link-static-page-sitemap-2';
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorMenuItemLink = `${this.languageSelectorDiv} ul li a[data-iso-code='%LANG']`;
  }

  /**
   * go to the home page
   */
  async goToHomePage() {
    await this.waitForSelectorAndClick(this.desktopLogo);
  }

  /**
   * Go to category
   * @param categoryID, category id from the BO
   */
  async goToCategory(categoryID) {
    await this.waitForSelectorAndClick(this.categoryMenu.replace('%ID', categoryID));
  }

  /**
   * Go to subcategory
   * @param categoryID, category id from the BO
   * @param subCategoryID, subcategory id from the BO
   */
  async goToSubCategory(categoryID, subCategoryID) {
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

  /**
   * Logout from FO
   * @return {Promise<void>}
   */
  async logout() {
    await Promise.all([
      this.page.click(this.logoutLink),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Change language in FO
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(lang = 'en') {
    await Promise.all([
      this.page.click(this.languageSelectorExpandIcon),
      this.page.waitForSelector(this.languageSelectorMenuItemLink.replace('%LANG', lang)),
    ]);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.languageSelectorMenuItemLink.replace('%LANG', lang)),
    ]);
  }
};
