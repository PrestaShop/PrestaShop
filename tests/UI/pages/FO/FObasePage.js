require('module-alias/register');
const CommonPage = require('@pages/commonPage');

module.exports = class FOBasePage extends CommonPage {
  constructor() {
    super();

    // Selectors for home page
    this.content = '#content';
    this.desktopLogo = '#_desktop_logo';
    this.desktopLogoLink = `${this.desktopLogo} a`;
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
    this.cartLink = '#_desktop_cart a';
    this.userInfoLink = '#_desktop_user_info';
    this.logoutLink = `${this.userInfoLink} .user-info a.logout`;
    this.contactLink = '#contact-link';
    this.categoryMenu = id => `#category-${id} a`;
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.defaultLanguageSpan = `${this.languageSelectorDiv} button span`;
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorMenuItemLink = language => `${this.languageSelectorDiv} ul li a[data-iso-code='${language}']`;
    this.currencySelectorDiv = '#_desktop_currency_selector';
    this.defaultCurrencySpan = `${this.currencySelectorDiv} button span`;
    this.currencySelect = 'select[aria-labelledby=\'currency-selector-label\']';
    // footer
    this.siteMapLink = '#link-static-page-sitemap-2';
    // footer links
    this.footerLinksDiv = '#footer div.links';
    this.wrapperDiv = position => `${this.footerLinksDiv}:nth-child(1) > div > div.wrapper:nth-child(${position})`;
    this.wrapperTitle = position => `${this.wrapperDiv(position)} p`;
    this.wrapperSubmenu = position => `${this.wrapperDiv(position)} ul[id*='footer_sub_menu']`;
    this.wrapperSubmenuItemLink = position => `${this.wrapperSubmenu(position)} li a`;
  }

  /**
   * Go to the home page
   * @param page
   * @returns {Promise<void>}
   */
  async goToHomePage(page) {
    await this.waitForVisibleSelector(page, this.desktopLogo);
    await this.clickAndWaitForNavigation(page, this.desktopLogoLink);
  }

  /**
   * Go to category
   * @param page
   * @param categoryID, category id from the BO
   * @returns {Promise<void>}
   */
  async goToCategory(page, categoryID) {
    await this.waitForSelectorAndClick(page, this.categoryMenu(categoryID));
  }

  /**
   * Go to subcategory
   * @param page
   * @param categoryID, category id from the BO
   * @param subCategoryID, subcategory id from the BO
   * @returns {Promise<void>}
   */
  async goToSubCategory(page, categoryID, subCategoryID) {
    await page.hover(this.categoryMenu(categoryID));
    await this.waitForSelectorAndClick(page, this.categoryMenu(subCategoryID));
  }

  /**
   * Go to login Page
   * @param page
   * @return {Promise<void>}
   */
  async goToLoginPage(page) {
    await this.clickAndWaitForNavigation(page, this.userInfoLink);
  }

  /**
   * Check if customer is connected
   * @param page
   * @return {Promise<boolean>}
   */
  async isCustomerConnected(page) {
    return this.elementVisible(page, this.logoutLink, 1000);
  }

  /**
   * Logout from FO
   * @param page
   * @return {Promise<void>}
   */
  async logout(page) {
    await this.clickAndWaitForNavigation(page, this.logoutLink);
  }

  /**
   * Change language in FO
   * @param page
   * @param lang
   * @return {Promise<void>}
   */
  async changeLanguage(page, lang = 'en') {
    await Promise.all([
      page.click(this.languageSelectorExpandIcon),
      this.waitForVisibleSelector(page, this.languageSelectorMenuItemLink(lang)),
    ]);
    await this.clickAndWaitForNavigation(page, this.languageSelectorMenuItemLink(lang));
  }

  /**
   * Get shop language
   * @param page
   * @returns {Promise<string>}
   */
  getShopLanguage(page) {
    return this.getTextContent(page, this.defaultLanguageSpan);
  }


  /**
   * Return true if language exist in FO
   * @param page
   * @param lang
   * @return {Promise<boolean>}
   */
  async languageExists(page, lang = 'en') {
    await page.click(this.languageSelectorExpandIcon);
    return this.elementVisible(page, this.languageSelectorMenuItemLink(lang), 1000);
  }

  /**
   * Change currency in FO
   * @param page
   * @param currency
   * @return {Promise<void>}
   */
  async changeCurrency(page, currency = 'EUR €') {
    await Promise.all([
      this.selectByVisibleText(page, this.currencySelect, currency),
      page.waitForNavigation('newtorkidle'),
    ]);
  }

  /**
   * Get text content of footer links
   * @param page
   * @param position, position of links
   * @return {Promise<!Promise<!Object|undefined>|any>}
   */
  async getFooterLinksTextContent(page, position) {
    return page.$$eval(
      this.wrapperSubmenuItemLink(position),
      all => all.map(el => el.textContent.trim()),
    );
  }

  /**
   * Get Title of Block that contains links in footer
   * @param page
   * @param position
   * @returns {Promise<string>}
   */
  async getFooterLinksBlockTitle(page, position) {
    return this.getTextContent(page, this.wrapperTitle(position));
  }

  /**
   * Get cart notifications number
   * @param page
   * @returns {Promise<number>}
   */
  async getCartNotificationsNumber(page) {
    return this.getNumberFromText(page, this.cartProductsCount);
  }

  /**
   * Go to siteMap page
   * @param page
   * @returns {Promise<void>}
   */
  async goToSiteMapPage(page) {
    await this.clickAndWaitForNavigation(page, this.siteMapLink);
  }

  /**
   * Go to cart page
   * @param page
   * @returns {Promise<void>}
   */
  async goToCartPage(page) {
    await this.clickAndWaitForNavigation(page, this.cartLink);
  }

  /**
   * Go to Fo page
   * @param page
   * @return {Promise<void>}
   */
  async goToFo(page) {
    await this.goTo(page, global.FO.URL);
  }

  /**
   * Get default currency
   * @param page
   * @returns {Promise<string>}
   */
  getDefaultCurrency(page) {
    return this.getTextContent(page, this.defaultCurrencySpan);
  }

  /**
   * CLick on siteMap link on footer and go to page
   * @param page
   * @return {Promise<void>}
   */
  async goToSitemapPage(page) {
    await this.clickAndWaitForNavigation(page, this.siteMapLink);
  }
};
