require('module-alias/register');
const CommonPage = require('@pages/commonPage');

module.exports = class FOBasePage extends CommonPage {
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
   * @param page
   * @return {Promise<void>}
   */
  async goToFo(page) {
    await this.goTo(page, global.FO.URL);
  }

  // Header methods
  /**
   * Go to header link
   * @param page
   * @param link
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
   * @param page
   * @returns {Promise<void>}
   */
  async goToHomePage(page) {
    await this.waitForVisibleSelector(page, this.desktopLogo);
    await this.clickOnHeaderLink(page, 'Logo');
  }

  /**
   * Go to login Page
   * @param page
   * @return {Promise<void>}
   */
  async goToLoginPage(page) {
    await this.clickOnHeaderLink(page, 'Sign in');
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
   * Check if customer is connected
   * @param page
   * @return {Promise<boolean>}
   */
  async isCustomerConnected(page) {
    return this.elementVisible(page, this.logoutLink, 1000);
  }

  /**
   * Click on link to go to account page
   * @param page
   * @return {Promise<void>}
   */
  async goToMyAccountPage(page) {
    await this.clickAndWaitForNavigation(page, this.accountLink);
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
   * Get default currency
   * @param page
   * @returns {Promise<string>}
   */
  getDefaultCurrency(page) {
    return this.getTextContent(page, this.defaultCurrencySpan);
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
   * Get cart notifications number
   * @param page
   * @returns {Promise<number>}
   */
  async getCartNotificationsNumber(page) {
    return this.getNumberFromText(page, this.cartProductsCount);
  }

  /**
   * Go to cart page
   * @param page
   * @returns {Promise<void>}
   */
  async goToCartPage(page) {
    await this.clickOnHeaderLink(page, 'Cart');
  }

  /**
   * Get autocomplete search result
   * @param page
   * @param productName
   * @returns {Promise<*>}
   */
  async getAutocompleteSearchResult(page, productName) {
    await this.setValue(page, this.searchInput, productName);
    await page.waitForTimeout(2000);
    return this.getTextContent(page, this.autocompleteSearchResult);
  }

  /**
   * Search product
   * @param page
   * @param productName
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
   * @param page
   * @param position
   * @returns {Promise<string>}
   */
  async getFooterLinksBlockTitle(page, position) {
    return this.getTextContent(page, this.wrapperTitle(position));
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
   * Get store information
   * @param page
   * @returns {Promise<string>}
   */
  async getStoreInformation(page) {
    return this.getTextContent(page, this.wrapperContactBlockDiv);
  }

  /**
   * Go to footer link
   * @param page
   * @param pageTitle
   * @returns {Promise<void>}
   */
  async goToFooterLink(page, pageTitle) {
    let selector;

    switch (pageTitle) {
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
        throw new Error(`The page ${pageTitle} was not found`);
    }
    return this.clickAndWaitForNavigation(page, selector);
  }
};
