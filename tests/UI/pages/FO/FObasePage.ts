// Import pages
import CommonPage from '@pages/commonPage';

import type {Page} from 'playwright';

/**
 * FO parent page, contains functions that can be used on all FO page
 * @class
 * @extends CommonPage
 */
export default class FOBasePage extends CommonPage {
  public readonly content: string;

  private readonly desktopLogo: string;

  private readonly desktopLogoLink: string;

  private readonly breadCrumbLink: (link: string) => string;

  private readonly cartProductsCount: string;

  private readonly cartLink: string;

  private readonly userInfoLink: string;

  private readonly accountLink: string;

  private readonly logoutLink: string;

  private readonly contactLink: string;

  private readonly categoryMenu: (id: number) => string;

  private readonly languageSelectorDiv: string;

  private readonly defaultLanguageSpan: string;

  private readonly languageSelectorExpandIcon: string;

  private readonly languageSelectorList: string;

  private readonly languageSelectorMenuItemLink: (language: string) => string;

  private readonly currencySelectorDiv: string;

  private readonly defaultCurrencySpan: string;

  private readonly currencySelectorExpandIcon: string;

  private readonly currencySelectorMenuItemLink: (currency: string) => string;

  private readonly currencySelect: string;

  private readonly searchInput: string;

  private readonly autocompleteSearchResult: string;

  private readonly autocompleteSearchResultItem: string;

  private readonly autocompleteSearchResultItemLink: (nthChild: number) => string;

  private readonly pricesDropLink: string;

  private readonly newProductsLink: string;

  private readonly bestSalesLink: string;

  private readonly deliveryLink: string;

  private readonly legalNoticeLink: string;

  private readonly termsAndConditionsOfUseLink: string;

  private readonly aboutUsLink: string;

  private readonly securePaymentLink: string;

  private readonly contactUsLink: string;

  private readonly siteMapLink: string;

  private readonly storesLink: string;

  private readonly footerAccountList: string;

  private readonly informationLink: string;

  private readonly orderTrackingLink: string;

  private readonly signInLink: string;

  private readonly createAccountLink: string;

  private readonly addressesLink: string;

  private readonly addFirstAddressLink: string;

  private readonly ordersLink: string;

  private readonly creditSlipsLink: string;

  private readonly vouchersLink: string;

  private readonly wishListLink: string;

  private readonly signOutLink: string;

  private readonly wrapperContactBlockDiv: string;

  private readonly footerLinksDiv: string;

  private readonly wrapperDiv: (position: number) => string;

  private readonly wrapperTitle: (position:number) => string;

  private readonly wrapperSubmenu: (position: number) => string;

  private readonly wrapperSubmenuItemLink: (position: number) => string;

  private readonly copyrightLink: string;

  protected readonly alertSuccessBlock: string;

  protected readonly notificationsBlock: string;

  protected readonly userMenuDropdown: string;

  protected readonly currencySelector: string;

  protected readonly languageSelector: string;

  private readonly cartProductsCountHummingbird: string;

  protected readonly navbarLink: string;

  protected readonly hSearchInput: string;

  protected theme: string;

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
    this.breadCrumbLink = (link) => `#wrapper nav.breadcrumb a[href*=${link}]`;
    this.cartProductsCount = '#_desktop_cart .cart-products-count';
    this.cartLink = '#_desktop_cart a';
    this.userInfoLink = '#_desktop_user_info';
    this.accountLink = `${this.userInfoLink} .user-info a[href*="/my-account"]`;
    this.logoutLink = `${this.userInfoLink} .user-info a[href*="/?mylogout="]`;
    this.contactLink = '#contact-link';
    this.categoryMenu = (id) => `#category-${id} a`;
    this.languageSelectorDiv = '#_desktop_language_selector';
    this.defaultLanguageSpan = `${this.languageSelectorDiv} button span`;
    this.languageSelectorExpandIcon = `${this.languageSelectorDiv} i.expand-more`;
    this.languageSelectorList = `${this.languageSelectorDiv} .js-dropdown.open`;
    this.languageSelectorMenuItemLink = (language) => `${this.languageSelectorDiv} ul li `
      + `a[data-iso-code='${language}']`;
    this.currencySelectorDiv = '#_desktop_currency_selector';
    this.defaultCurrencySpan = `${this.currencySelectorDiv} button span`;
    this.currencySelectorExpandIcon = `${this.currencySelectorDiv} i.expand-more`;
    this.currencySelectorMenuItemLink = (currency) => `${this.currencySelectorExpandIcon} ul li a[title='${currency}']`;
    this.currencySelect = 'select[aria-labelledby=\'currency-selector-label\']';
    this.searchInput = '#search_widget input.ui-autocomplete-input';
    this.autocompleteSearchResult = '.ui-autocomplete';
    this.autocompleteSearchResultItem = `${this.autocompleteSearchResult} .ui-menu-item`;
    this.autocompleteSearchResultItemLink = (nthChild) => `${this.autocompleteSearchResult} `
      + `.ui-menu-item:nth-child(${nthChild}) a`;

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
    this.informationLink = `${this.footerAccountList} a[title='Information']`;
    this.orderTrackingLink = `${this.footerAccountList} a[title='Order tracking']`;
    this.signInLink = `${this.footerAccountList} a[href*='/my-account']`;
    this.createAccountLink = `${this.footerAccountList} a[title='Create account']`;
    this.addressesLink = `${this.footerAccountList} a[title='Addresses']`;
    this.addFirstAddressLink = `${this.footerAccountList} a[title='Add first address']`;
    this.ordersLink = `${this.footerAccountList} a[title='Orders']`;
    this.creditSlipsLink = `${this.footerAccountList} a[title='Credit slips']`;
    this.vouchersLink = `${this.footerAccountList} a[title='Vouchers']`;
    this.wishListLink = `${this.footerAccountList} a[title='My wishlists']`;
    this.signOutLink = `${this.footerAccountList} a[title='Log me out']`;

    // Store information
    this.wrapperContactBlockDiv = '#footer div.block-contact';

    this.footerLinksDiv = '#footer .links';
    this.wrapperDiv = (position) => `${this.footerLinksDiv} .wrapper:nth-child(${position})`;
    this.wrapperTitle = (position) => `${this.wrapperDiv(position)} p`;
    this.wrapperSubmenu = (position) => `${this.wrapperDiv(position)} ul[id*='footer_sub_menu']`;
    this.wrapperSubmenuItemLink = (position) => `${this.wrapperSubmenu(position)} li a`;

    // Copyright
    this.copyrightLink = '#footer div.footer-container a[href*="www.prestashop-project.org"]';

    // Alert block selectors
    this.alertSuccessBlock = '.alert-success ul li';
    this.notificationsBlock = '#notifications';

    // Hummingbird
    this.userMenuDropdown = '#userMenuButton';
    this.currencySelector = '#currency-selector';
    this.languageSelector = '#language-selector';
    this.cartProductsCountHummingbird = '#_desktop_cart .header-block__action-btn span.header-block__badge';
    this.navbarLink = '.navbar-brand';
    this.hSearchInput = '#search_widget .js-search-input';

    this.theme = 'classic';
  }

  // Header methods
  /**
   * Go to header link
   * @param page {Page} Browser tab
   * @param link {string} Header selector that contain link to click on to
   * @param hasPageChange {boolean}
   * @returns {Promise<void>}
   */
  async clickOnHeaderLink(page: Page, link: string, hasPageChange: boolean = true): Promise<void> {
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
        selector = this.theme === 'hummingbird' ? this.navbarLink : this.desktopLogoLink;
        break;

      default:
        throw new Error(`The page ${link} was not found`);
    }

    if (hasPageChange) {
      return this.clickAndWaitForURL(page, selector);
    }
    return this.clickAndWaitForLoadState(page, selector);
  }

  /**
   * Click on bread crumb link
   * @param page {Page} Browser tab
   * @param link {string} Link to click on
   * @returns {Promise<void>}
   */
  async clickOnBreadCrumbLink(page: Page, link: string): Promise<void> {
    await this.clickAndWaitForURL(page, this.breadCrumbLink(link));
  }

  /**
   * Go to the home page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToHomePage(page: Page): Promise<void> {
    if (this.theme === 'hummingbird') {
      await this.waitForVisibleSelector(page, this.navbarLink);
      await this.clickAndWaitForLoadState(page, this.navbarLink);
      return;
    }

    await this.waitForVisibleSelector(page, this.desktopLogo);
    await this.clickAndWaitForLoadState(page, this.desktopLogoLink);
  }

  /**
   * Go to login Page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToLoginPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.userInfoLink);
  }

  /**
   * Logout from FO
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async logout(page: Page): Promise<void> {
    if (this.theme === 'hummingbird') {
      await page.click(this.userMenuDropdown);
      await this.clickAndWaitForLoadState(page, this.logoutLink);
      await this.elementNotVisible(page, this.logoutLink, 2000);

      return;
    }
    await this.clickAndWaitForLoadState(page, this.logoutLink);
  }

  /**
   * Check if customer is connected
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCustomerConnected(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.theme === 'hummingbird' ? this.userMenuDropdown : this.logoutLink, 1000);
  }

  /**
   * Click on link to go to account page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToMyAccountPage(page: Page): Promise<void> {
    if (this.theme === 'hummingbird') {
      await page.click(this.userMenuDropdown);
      await this.clickAndWaitForURL(page, this.accountLink);

      return;
    }
    await this.clickAndWaitForURL(page, this.accountLink);
  }

  /**
   * Is language list visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isLanguageListVisible(page: Page): Promise<boolean> {
    if (this.theme === 'hummingbird') {
      return this.elementVisible(page, this.languageSelector, 1000);
    }
    return this.elementVisible(page, this.languageSelectorExpandIcon, 1000);
  }

  /**
   * Get shop language
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getShopLanguage(page: Page): Promise<string> {
    return this.getAttributeContent(page, 'html[lang]', 'lang');
  }

  /**
   * Change language in FO
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose on the select (ex: en or fr)
   * @return {Promise<void>}
   */
  async changeLanguage(page: Page, lang: string = 'en'): Promise<void> {
    if (this.theme === 'hummingbird') {
      const langOptions = await page.$$(`${this.languageSelector} option`);

      // eslint-disable-next-line no-restricted-syntax
      for (const [keyOption, langOption] of Object.entries(langOptions)) {
        if ((await langOption.getAttribute('data-iso-code')) === lang) {
          await page.selectOption(this.languageSelector, {index: parseInt(keyOption, 10)});
          return;
        }
      }

      return;
    }
    await Promise.all([
      page.click(this.languageSelectorExpandIcon),
      this.waitForVisibleSelector(page, this.languageSelectorList),
    ]);
    await this.clickAndWaitForLoadState(page, this.languageSelectorMenuItemLink(lang));
  }

  /**
   * Get default shop language
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDefaultShopLanguage(page: Page): Promise<string> {
    if (this.theme === 'hummingbird') {
      return page
        .locator(this.languageSelector)
        .evaluate((el: HTMLSelectElement): string => el.options[el.options.selectedIndex].textContent ?? '');
    }
    return this.getTextContent(page, this.defaultLanguageSpan);
  }

  /**
   * Return true if language exist in FO
   * @param page {Page} Browser tab
   * @param lang {string} Language to check on the select (ex: en or fr)
   * @return {Promise<boolean>}
   */
  async languageExists(page: Page, lang: string = 'en'): Promise<boolean> {
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
  async changeCurrency(page: Page, isoCode: string = 'EUR', symbol: string = '€'): Promise<void> {
    const currency = isoCode === symbol ? isoCode : `${isoCode} ${symbol}`;

    if (this.theme === 'hummingbird') {
      const langOptions = await page.$$(`${this.currencySelector} option`);

      // eslint-disable-next-line no-restricted-syntax
      for (const [keyOption, langOption] of Object.entries(langOptions)) {
        if ((await langOption.textContent()) === currency) {
          await page.selectOption(this.currencySelector, {index: parseInt(keyOption, 10)});
          return;
        }
      }

      return;
    }
    // If isoCode and symbol are the same, only isoCode id displayed in FO
    const currentUrl: string = page.url();

    await Promise.all([
      this.selectByVisibleText(page, this.currencySelect, currency, true),
      page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'}),
    ]);
  }

  /**
   * Is currency dropdownExist
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCurrencyDropdownExist(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.currencySelectorExpandIcon, 1000);
  }

  /**
   * Get if currency exists on dropdown
   * @param page {Page} Browser tab
   * @param currencyName {string} Name of the currency to check
   * @returns {Promise<boolean>}
   */
  async currencyExists(page: Page, currencyName: string = 'Euro'): Promise<boolean> {
    await page.click(this.currencySelectorExpandIcon);
    return this.elementVisible(page, this.currencySelectorMenuItemLink(currencyName), 1000);
  }

  /**
   * Get default currency
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getDefaultCurrency(page: Page): Promise<string> {
    if (this.theme === 'hummingbird') {
      return page
        .locator(this.currencySelector)
        .evaluate((el: HTMLSelectElement): string => el.options[el.options.selectedIndex].textContent ?? '');
    }
    return this.getTextContent(page, this.defaultCurrencySpan);
  }

  /**
   * Go to category
   * @param page {Page} Browser tab
   * @param categoryID {number} Category id from the BO
   * @returns {Promise<void>}
   */
  async goToCategory(page: Page, categoryID: number): Promise<void> {
    await this.clickAndWaitForURL(page, this.categoryMenu(categoryID));
  }

  /**
   * Go to subcategory
   * @param page {Page} Browser tab
   * @param categoryID {number} Category id from the BO
   * @param subCategoryID {number} Subcategory id from the BO
   * @returns {Promise<void>}
   */
  async goToSubCategory(page: Page, categoryID: number, subCategoryID: number): Promise<void> {
    await page.hover(this.categoryMenu(categoryID));
    await this.clickAndWaitForURL(page, this.categoryMenu(subCategoryID));
  }

  /**
   * Get store information
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getStoreInformation(page: Page): Promise<string> {
    return this.getTextContent(page, this.wrapperContactBlockDiv);
  }

  /**
   * Get cart notifications number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getCartNotificationsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(
      page,
      this.theme === 'hummingbird' ? this.cartProductsCountHummingbird : this.cartProductsCount,
      2000,
    );
  }

  /**
   * Go to cart page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCartPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.cartLink);
  }

  /**
   * Close the  autocomplete search result
   * @param page {Page} Browser tab
   * @returns {void}
   */
  async closeAutocompleteSearch(page: Page): Promise<void> {
    await page.keyboard.press('Escape');
  }

  /**
   * Check if there are autocomplete search result
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isAutocompleteSearchResultVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.autocompleteSearchResult, 2000);
  }

  /**
   * Check if there are autocomplete search result
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<boolean>}
   */
  async hasAutocompleteSearchResult(page: Page, productName:string): Promise<boolean> {
    await this.setValue(page, this.searchInput, productName);
    return this.isAutocompleteSearchResultVisible(page);
  }

  /**
   * Get autocomplete search result
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<string>}
   */
  async getAutocompleteSearchResult(page: Page, productName: string): Promise<string> {
    await this.setValue(page, this.searchInput, productName);
    await this.waitForVisibleSelector(page, this.autocompleteSearchResult);
    return this.getTextContent(page, this.autocompleteSearchResult);
  }

  /**
   * Count autocomplete search result
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<number>}
   */
  async countAutocompleteSearchResult(page: Page, productName: string): Promise<number> {
    await this.setValue(page, this.searchInput, productName);
    await this.waitForVisibleSelector(page, this.autocompleteSearchResultItem);
    return page.$$eval(this.autocompleteSearchResultItem, (all) => all.length);
  }

  /**
   * Search product
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @returns {Promise<void>}
   */
  async searchProduct(page: Page, productName: string): Promise<void > {
    const currentUrl: string = page.url();

    await this.setValue(page, this.theme === 'hummingbird' ? this.hSearchInput : this.searchInput, productName);
    await page.keyboard.press('Enter');
    await page.waitForURL((url: URL): boolean => url.toString() !== currentUrl, {waitUntil: 'networkidle'});
  }

  /**
   * Click autocomplete search on the nth result
   * @param page {Page} Browser tab
   * @param productName {string} Product name to search
   * @param nthResult {number} Nth result to click
   * @returns {Promise<number>}
   */
  async clickAutocompleteSearchResult(page: Page, productName: string, nthResult: number): Promise<void> {
    await this.setValue(page, this.searchInput, productName);
    await this.waitForVisibleSelector(page, this.autocompleteSearchResultItem);
    await this.clickAndWaitForURL(page, this.autocompleteSearchResultItemLink(nthResult));
  }

  // Footer methods
  /**
   * Get Title of Block that contains links in footer
   * @param page {Page} Browser tab
   * @param position {number} Position of the links on footer
   * @returns {Promise<string>}
   */
  async getFooterLinksBlockTitle(page: Page, position: number): Promise<string> {
    return this.getTextContent(page, this.wrapperTitle(position));
  }

  /**
   * Get text content of footer links
   * @param page {Page} Browser tab
   * @param position {number} Position of the links on footer
   * @return {Promise<Array<string>>}
   */
  async getFooterLinksTextContent(page: Page, position: number): Promise<Array<string>> {
    return page.$$eval(
      this.wrapperSubmenuItemLink(position),
      (all) => all.map((el) => (el.textContent ?? '').trim()),
    );
  }

  /**
   * Go to footer link
   * @param page {Page} Browser tab
   * @param textSelector {string} String displayed on footer link to click on
   * @returns {Promise<void>}
   */
  async goToFooterLink(page: Page, textSelector: string): Promise<void> {
    let selector;

    switch (textSelector) {
      case 'Prices drop':
        selector = this.pricesDropLink;
        break;

      case 'New products':
        selector = this.newProductsLink;
        break;

      case 'Best sellers':
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

      case 'Information':
        selector = this.informationLink;
        break;

      case 'Order tracking':
        selector = this.orderTrackingLink;
        break;

      case 'Sign in':
        selector = this.signInLink;
        break;

      case 'Create account':
        selector = this.createAccountLink;
        break;

      case 'Addresses':
        selector = this.addressesLink;
        break;

      case 'Add first address':
        selector = this.addFirstAddressLink;
        break;

      case 'Orders':
        selector = this.ordersLink;
        break;

      case 'Credit slips':
        selector = this.creditSlipsLink;
        break;

      case 'Vouchers':
        selector = this.vouchersLink;
        break;

      case 'Wishlist':
        selector = this.wishListLink;
        break;

      case 'Sign out':
        selector = this.signOutLink;
        break;

      default:
        throw new Error(`The page ${textSelector} was not found`);
    }

    return this.clickAndWaitForURL(page, selector);
  }

  /**
   * Get copyright
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCopyright(page: Page): Promise<string> {
    return this.getTextContent(page, this.copyrightLink);
  }

  /**
   * Check that currency is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCurrencyVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.currencySelectorDiv, 1000);
  }

  /**
   * Get the value of an input
   *
   * @param page {Page} Browser tab
   * @param input {string} ID of the input
   * @returns {Promise<string>}
   */
  async getInputValue(page: Page, input: string): Promise<string> {
    return page.inputValue(input);
  }

  /**
   * Get the value of an input
   *
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getSearchValue(page: Page): Promise<string> {
    return page.inputValue(this.searchInput);
  }
}

module.exports = FOBasePage;
