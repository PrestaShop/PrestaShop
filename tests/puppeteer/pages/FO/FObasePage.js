require('module-alias/register');
const CommonPage = require('@pages/commonPage');

module.exports = class Home extends CommonPage {
  constructor(page) {
    super(page);

    // Selectors for home page
    this.content = '#content';
    this.desktopLogo = '#_desktop_logo';
    this.desktopLogoLink = `${this.desktopLogo} a`;
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
    this.cartLink = '#_desktop_cart a';
    this.userInfoLink = '#_desktop_user_info';
    this.logoutLink = `${this.userInfoLink} .user-info a.logout`;
    this.contactLink = '#contact-link';
    this.categoryMenu = '#category-%ID > a';
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.defaultLanguageSpan = `${this.languageSelectorDiv} button span`;
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorMenuItemLink = `${this.languageSelectorDiv} ul li a[data-iso-code='%LANG']`;
    this.currencySelect = 'select[aria-labelledby=\'currency-selector-label\']';

    // footer
    this.siteMapLink = '#link-static-page-sitemap-2';
    // footer links
    this.footerLinksDiv = '#footer div.links';
    this.wrapperDiv = `${this.footerLinksDiv}:nth-child(1) > div > div.wrapper:nth-child(%POSITION)`;
    this.wrapperTitle = `${this.wrapperDiv} p`;
    this.wrapperSubmenu = `${this.wrapperDiv} ul[id*='footer_sub_menu']`;
    this.wrapperSubmenuItemLink = `${this.wrapperSubmenu} li a`;
  }

  /**
   * go to the home page
   */
  async goToHomePage() {
    await this.waitForVisibleSelector(this.desktopLogo);
    await this.clickAndWaitForNavigation(this.desktopLogoLink);
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
    await this.clickAndWaitForNavigation(this.userInfoLink);
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
    await this.clickAndWaitForNavigation(this.logoutLink);
  }

  /**
   * Change language in FO
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(lang = 'en') {
    await Promise.all([
      this.page.click(this.languageSelectorExpandIcon),
      this.waitForVisibleSelector(this.languageSelectorMenuItemLink.replace('%LANG', lang)),
    ]);
    await this.clickAndWaitForNavigation(this.languageSelectorMenuItemLink.replace('%LANG', lang));
  }

  /**
   * Get shop language
   * @returns {Promise<string>}
   */
  getShopLanguage() {
    return this.getTextContent(this.defaultLanguageSpan);
  }


  /**
   * Return true if language exist in FO
   * @param lang
   * @return {Promise<boolean|true>}
   */
  async languageExists(lang = 'en') {
    await this.page.click(this.languageSelectorExpandIcon);
    return this.elementVisible(this.languageSelectorMenuItemLink.replace('%LANG', lang), 1000);
  }

  /**
   * Change currency in FO
   * @param currency
   * @return {Promise<void>}
   */
  async changeCurrency(currency = 'EUR €') {
    await Promise.all([
      this.selectByVisibleText(this.currencySelect, currency),
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
    ]);
  }

  /**
   * Get text content of footer links
   * @param position, position of links
   * @return {Promise<!Promise<!Object|undefined>|any>}
   */
  async getFooterLinksTextContent(position) {
    return this.page.$$eval(
      this.wrapperSubmenuItemLink.replace('%POSITION', position),
      all => all.map(el => el.textContent.trim()),
    );
  }

  /**
   * Get Title of Block that contains links in footer
   * @param position
   * @return {Promise<textContent>}
   */
  async getFooterLinksBlockTitle(position) {
    return this.getTextContent(this.wrapperTitle.replace('%POSITION', position));
  }

  /**
   * Get cart notifications number
   * @returns {Promise<integer>}
   */
  async getCartNotificationsNumber() {
    return this.getNumberFromText(this.cartProductsCount);
  }

  /**
   * Go to siteMap page
   * @returns {Promise<void>}
   */
  async goToSiteMapPage() {
    await this.clickAndWaitForNavigation(this.siteMapLink);
  }

  /**
   * Go to cart page
   * @returns {Promise<void>}
   */
  async goToCartPage() {
    await this.clickAndWaitForNavigation(this.cartLink);
  }

  /**
   * Go to Fo page
   * @return {Promise<void>}
   */
  async goToFo() {
    await this.goTo(global.FO.URL);
  }
};
