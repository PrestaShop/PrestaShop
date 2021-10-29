require('module-alias/register');
const CommonPage = require('@pages/commonPage');

/**
 * FO parent page, contains functions that can be used on all FO page
 * @class
 * @extends CommonPage
 */
class FOBasePage extends CommonPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on all FO pages
   */
  constructor() {
    super();

    // Selectors for home page
    // Header links
    this.content = '#content';
    this.desktopLogo = '#_desktop_logo';
    this.desktopLogoLink = `${this.desktopLogo} a`;
    this.cartProductsCount = '#_desktop_cart span.cart-products-count';
    this.cartLink = '#_desktop_cart a';
    this.userInfoLink = '#_desktop_user_info';
    this.accountLink = `${this.userInfoLink} .user-info a.account`;
    this.logoutLink = `${this.userInfoLink} .user-info a.logout`;
    this.contactLink = '#contact-link';
    this.categoryMenu = id => `#category-${id} a`;
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.defaultLanguageSpan = `${this.languageSelectorDiv} button span`;
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorMenuItemLink = language => `${this.languageSelectorDiv} ul li a[data-iso-code='${language}']`;
    this.currencySelectorDiv = '#_desktop_currency_selector';
    this.defaultCurrencySpan = `${this.currencySelectorDiv} button span`;
    this.currencySelectorExpandIcon = `${this.currencySelectorDiv} i.expand-more`;
    this.currencySelectorMenuItemLink = currency => `${this.currencySelectorExpandIcon} ul li a[title='${currency}']`;
    this.currencySelect = 'select[aria-labelledby=\'currency-selector-label\']';
    this.searchInput = '#search_widget input.ui-autocomplete-input';
    this.autocompleteSearchResult = '.ui-autocomplete';

    // Footer links
    // Products links selectors
    this.pricesDropLink = '#link-product-page-prices-drop-1';
    this.newProductsLink = '#link-product-page-new-products-1';
    this.bestSalesLink = '#link-product-page-best-sales-1';
    // Our company links selectors
    this.deliveryLink = '#link-cms-page-1-2';
    this.legalNoticeLink = '#link-cms-page-2-2';
    this.termsAndConditionsOfUseLink = '#link-cms-page-3-2';
    this.aboutUsLink = '#link-cms-page-4-2';
    this.securePaymentLink = '#link-cms-page-5-2';
    this.contactUsLink = '#link-static-page-contact-2';
    this.siteMapLink = '#link-static-page-sitemap-2';
    this.storesLink = '#link-static-page-stores-2';
    // Your account links selectors
    this.footerAccountList = '#footer_account_list';
    this.personalInfoLink = `${this.footerAccountList} a[title='Personal info']`;
    this.ordersLink = `${this.footerAccountList} a[title='Orders']`;
    this.creditSlipsLink = `${this.footerAccountList} a[title='Credit slips']`;
    this.addressesLink = `${this.footerAccountList} a[title='Addresses']`;
    // Store information
    this.wrapperContactBlockDiv = '#footer div.block-contact';

    this.footerLinksDiv = '#footer div.links';
    this.wrapperDiv = position => `${this.footerLinksDiv}:nth-child(1) > div > div.wrapper:nth-child(${position})`;
    this.wrapperTitle = position => `${this.wrapperDiv(position)} p`;
    this.wrapperSubmenu = position => `${this.wrapperDiv(position)} ul[id*='footer_sub_menu']`;
    this.wrapperSubmenuItemLink = position => `${this.wrapperSubmenu(position)} li a`;

    // Alert block selectors
    this.alertSuccessBlock = '.alert-success ul li';
  }

  // Methods

  /**
   * Go to Fo page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToFo(page) {
    await this.goTo(page, global.FO.URL);
  }

  // Header methods
  /**
   * Go to header link
   * @param page {Page} Browser tab
   * @param link {string} Header selector that contain link to click on to
   * @returns {Promise<void>}
   */
  async clickOnHeaderLink(page, link) {
    let selector;

    switch (link) {
      case 'Contact us':
        selector = this.contactLink;
        break;

      case 'Sign in':
        selector = this.userInfoLink;
        break;

      case 'Cart':
        selector = this.cartLink;
        break;

      case 'Logo':
        selector = this.desktopLogoLink;
        break;

      default:
        throw new Error(`The page ${link} was not found`);
    }

    return this.clickAndWaitForNavigation(page, selector);
  }

  /**
   * Go to the home page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToHomePage(page) {
    await this.waitForVisibleSelector(page, this.desktopLogo);
    await this.clickAndWaitForNavigation(page, this.desktopLogoLink);
  }

  /**
   * Go to login Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToLoginPage(page) {
    await this.clickAndWaitForNavigation(page, this.userInfoLink);
  }

  /**
   * Logout from FO
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async logout(page) {
    await this.clickAndWaitForNavigation(page, this.logoutLink);
  }

  /**
   * Check if customer is connected
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCustomerConnected(page) {
    return this.elementVisible(page, this.logoutLink, 1000);
  }

  /**
   * Click on link to go to account page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToMyAccountPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountLink);
  }

  /**
   * Change language in FO
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose on the select (ex: en or fr)
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
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getShopLanguage(page) {
    return this.getTextContent(page, this.defaultLanguageSpan);
  }

  /**
   * Return true if language exist in FO
   * @param page {Page} Browser tab
   * @param lang {string} Language to check on the select (ex: en or fr)
   * @return {Promise<boolean>}
   */
  async languageExists(page, lang = 'en') {
    await page.click(this.languageSelectorExpandIcon);
    return this.elementVisible(page, this.languageSelectorMenuItemLink(lang), 1000);
  }

  /**
   * Change currency in FO
   * @param page {Page} Browser tab
   * @param isoCode {string} Iso code of the currency to choose
   * @param symbol {string} Symbol of the currency to choose
   * @return {Promise<void>}
   */
  async changeCurrency(page, isoCode = 'EUR', symbol = '€') {
    // If isoCode and symbol are the same, only isoCode id displayed in FO
    const currency = isoCode === symbol ? isoCode : `${isoCode} ${symbol}`;

    await Promise.all([
      this.selectByVisibleText(page, this.currencySelect, currency, true),
      page.waitForNavigation('newtorkidle'),
    ]);
  }

  /**
   * Get if currency exists on dropdown
   * @param page {Page} Browser tab
   * @param currencyName {string} Name of the currency to check
   * @returns {Promise<boolean>}
   */
  async currencyExists(page, currencyName = 'Euro') {
    await page.click(this.currencySelectorExpandIcon);
    return this.elementVisible(page, this.currencySelectorMenuItemLink(currencyName), 1000);
  }

  /**
   * Get default currency
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getDefaultCurrency(page) {
    return this.getTextContent(page, this.defaultCurrencySpan);
  }

  /**
   * Go to category
   * @param page {Page} Browser tab
   * @param categoryID {number} Category id from the BO
   * @returns {Promise<void>}
   */
  async goToCategory(page, categoryID) {
    await this.waitForSelectorAndClick(page, this.categoryMenu(categoryID));
  }

  /**
   * Go to subcategory
   * @param page {Page} Browser tab
   * @param categoryID {number} Category id from the BO
   * @param subCategoryID {number} Subcategory id from the BO
   * @returns {Promise<void>}
   */
  async goToSubCategory(page, categoryID, subCategoryID) {
    await page.hover(this.categoryMenu(categoryID));
    await this.clickAndWaitForNavigation(page, this.categoryMenu(subCategoryID));
  }

  /**
   * Get store information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getStoreInformation(page) {
    return this.getTextContent(page, this.wrapperContactBlockDiv);
  }

  /**
   * Get cart notifications number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCartNotificationsNumber(page) {
    return this.getNumberFromText(page, this.cartProductsCount, 2000);
  }

  /**
   * Go to cart page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCartPage(page) {
    await this.clickAndWaitForNavigation(page, this.cartLink);
  }

  /**
   * Get autocomplete search result
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<*>}
   */
  async getAutocompleteSearchResult(page, productName) {
    await this.setValue(page, this.searchInput, productName);
    await page.waitForTimeout(2000);
    return this.getTextContent(page, this.autocompleteSearchResult);
  }

  /**
   * Search product
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<void>}
   */
  async searchProduct(page, productName) {
    await this.setValue(page, this.searchInput, productName);
    await page.keyboard.press('Enter');
    await page.waitForNavigation('networkidle');
  }

  // Footer methods
  /**
   * Get Title of Block that contains links in footer
   * @param page {Page} Browser tab
   * @param position {number} Position of the links on footer
   * @returns {Promise<string>}
   */
  async getFooterLinksBlockTitle(page, position) {
    return this.getTextContent(page, this.wrapperTitle(position));
  }

  /**
   * Get text content of footer links
   * @param page {Page} Browser tab
   * @param position {number} Position of the links on footer
   * @return {Promise<Array<string>>}
   */
  async getFooterLinksTextContent(page, position) {
    return page.$$eval(
      this.wrapperSubmenuItemLink(position),
      all => all.map(el => el.textContent.trim()),
    );
  }

  /**
   * Go to footer link
   * @param page {Page} Browser tab
   * @param textSelector {string} String displayed on footer link to click on
   * @returns {Promise<void>}
   */
  async goToFooterLink(page, textSelector) {
    let selector;

    switch (textSelector) {
      case 'Prices drop':
        selector = this.pricesDropLink;
        break;

      case 'New products':
        selector = this.newProductsLink;
        break;

      case 'Best sales':
        selector = this.bestSalesLink;
        break;

      case 'Delivery':
        selector = this.deliveryLink;
        break;

      case 'Legal Notice':
        selector = this.legalNoticeLink;
        break;

      case 'Terms and conditions of use':
        selector = this.termsAndConditionsOfUseLink;
        break;

      case 'About us':
        selector = this.aboutUsLink;
        break;

      case 'Secure payment':
        selector = this.securePaymentLink;
        break;

      case 'Contact us':
        selector = this.contactUsLink;
        break;

      case 'Sitemap':
        selector = this.siteMapLink;
        break;

      case 'Stores':
        selector = this.storesLink;
        break;

      case 'Personal info':
        selector = this.personalInfoLink;
        break;

      case 'Orders':
        selector = this.ordersLink;
        break;

      case 'Credit slips':
        selector = this.creditSlipsLink;
        break;

      case 'Addresses':
        selector = this.addressesLink;
        break;

      default:
        throw new Error(`The page ${textSelector} was not found`);
    }
    return this.clickAndWaitForNavigation(page, selector);
  }
}

module.exports = FOBasePage;
