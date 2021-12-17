require('module-alias/register');
const CommonPage = require('@pages/commonPage');

/**
 * BO parent page, contains functions that can be used on all BO page
 * @class
 * @extends CommonPage
 */
class BOBasePage extends CommonPage {
  /**
   * @constructs
   * Setting up texts and selectors to use on all BO pages
   */
  constructor() {
    super();

    // Successful Messages
    this.successfulCreationMessage = 'Successful creation.';
    this.successfulUpdateMessage = 'Successful update.';
    this.successfulDeleteMessage = 'Successful deletion.';
    this.successfulMultiDeleteMessage = 'The selection has been successfully deleted.';

    // Access denied message
    this.accessDeniedMessage = 'Access denied';
    this.pageNotFoundMessage = 'Page not found';

    // top navbar
    this.userProfileIconNonMigratedPages = '#employee_infos';
    this.userProfileIcon = '#header_infos #header-employee-container';
    this.userProfileLogoutLink = 'a#header_logout';
    this.shopVersionBloc = '#shop_version';
    this.headerShopNameLink = '#header_shopname';
    this.quickAccessDropdownToggle = '#quick_select';
    this.quickAccessLink = idLink => `.quick-row-link:nth-child(${idLink})`;
    this.quickAddCurrentLink = '#quick-add-link';
    this.quickAccessRemoveLink = '#quick-remove-link';
    this.manageYourQuickAccessLink = '#quick-manage-link';

    // Header links
    this.helpButton = '#product_form_open_help';

    // left navbar
    // Dashboard
    this.dashboardLink = '#tab-AdminDashboard';

    // SELL
    // Orders
    this.ordersParentLink = 'li#subtab-AdminParentOrders';
    this.ordersLink = '#subtab-AdminOrders';
    // Invoices
    this.invoicesLink = '#subtab-AdminInvoices';
    // Credit slips
    this.creditSlipsLink = '#subtab-AdminSlip';
    // Delivery slips
    this.deliverySlipslink = '#subtab-AdminDeliverySlip';
    // Shopping carts
    this.shoppingCartsLink = '#subtab-AdminCarts';

    // Catalog
    this.catalogParentLink = 'li#subtab-AdminCatalog';
    // Products
    this.productsLink = '#subtab-AdminProducts';
    // Categories
    this.categoriesLink = '#subtab-AdminCategories';
    // Monitoring
    this.monitoringLink = '#subtab-AdminTracking';
    // Attributes and Features
    this.attributesAndFeaturesLink = '#subtab-AdminParentAttributesGroups';
    // Brands And Suppliers
    this.brandsAndSuppliersLink = '#subtab-AdminParentManufacturers';
    // files
    this.filesLink = '#subtab-AdminAttachments';
    // Discounts
    this.discountsLink = '#subtab-AdminParentCartRules';
    // Stocks
    this.stocksLink = '#subtab-AdminStockManagement';

    // Customers
    this.customersParentLink = 'li#subtab-AdminParentCustomer';
    this.customersLink = '#subtab-AdminCustomers';
    this.addressesLink = '#subtab-AdminAddresses';

    // Customer Service
    this.customerServiceParentLink = '#subtab-AdminParentCustomerThreads';
    this.customerServiceLink = '#subtab-AdminCustomerThreads';
    // Order Messages
    this.orderMessagesLink = '#subtab-AdminOrderMessage';
    // Merchandise returns
    this.merchandiseReturnsLink = '#subtab-AdminReturn';

    // Improve
    // Modules
    this.modulesParentLink = '#subtab-AdminParentModulesSf';
    this.moduleCatalogueLink = '#subtab-AdminParentModulesCatalog';
    this.moduleManagerLink = '#subtab-AdminModulesSf';

    // Design
    this.designParentLink = '#subtab-AdminParentThemes';
    // Email theme
    this.emailThemeLink = '#subtab-AdminParentMailTheme';
    // Pages
    this.pagesLink = '#subtab-AdminCmsContent';
    // Positions
    this.positionsLink = '#subtab-AdminModulesPositions';
    // Image settings
    this.imageSettingsLink = '#subtab-AdminImages';
    // Link widget
    this.linkWidgetLink = '#subtab-AdminLinkWidget';

    // Shipping
    this.shippingLink = '#subtab-AdminParentShipping';
    this.carriersLink = '#subtab-AdminCarriers';
    this.shippingPreferencesLink = '#subtab-AdminShipping';

    // Payment
    this.paymentParentLink = '#subtab-AdminParentPayment';
    // Preferences
    this.preferencesLink = '#subtab-AdminPaymentPreferences';

    // International
    this.internationalParentLink = '#subtab-AdminInternational';
    // Taxes
    this.taxesLink = '#subtab-AdminParentTaxes';
    // Localization
    this.localizationLink = '#subtab-AdminParentLocalization';
    // Locations
    this.locationsLink = '#subtab-AdminParentCountries';
    // Translations
    this.translationsLink = '#subtab-AdminTranslations';

    // Shop Parameters
    this.shopParametersParentLink = '#subtab-ShopParameters';
    // General
    this.shopParametersGeneralLink = '#subtab-AdminParentPreferences';
    // Order Settings
    this.orderSettingsLink = '#subtab-AdminParentOrderPreferences';
    // Product Settings
    this.productSettingsLink = '#subtab-AdminPPreferences';
    // Customer Settings
    this.customerSettingsLink = '#subtab-AdminParentCustomerPreferences';
    // Contact
    this.contactLink = '#subtab-AdminParentStores';
    // traffic and SEO
    this.trafficAndSeoLink = '#subtab-AdminParentMeta';
    // Search
    this.searchLink = '#subtab-AdminParentSearchConf';

    // Advanced Parameters
    this.advancedParametersLink = '#subtab-AdminAdvancedParameters';
    // E-mail
    this.emailLink = '#subtab-AdminEmails';
    // Import
    this.importLink = '#subtab-AdminImport';
    // Team
    this.teamLink = '#subtab-AdminParentEmployees';
    // Database
    this.databaseLink = '#subtab-AdminParentRequestSql';
    // Webservice
    this.webserviceLink = '#subtab-AdminWebservice';
    // Logs
    this.logsLink = '#subtab-AdminLogs';
    // Multistore
    this.multistoreLink = '#subtab-AdminShopGroup';
    // Deprecated tab used for regression test
    this.menuTabLink = '#subtab-AdminTabs';

    // welcome module
    this.onboardingCloseButton = 'button.onboarding-button-shut-down';
    this.onboardingStopButton = 'a.onboarding-button-stop';

    // Growls
    this.growlDiv = '#growls';
    this.growlDefaultDiv = '#growls-default';
    this.growlMessageBlock = `${this.growlDefaultDiv} .growl-message`;
    this.growlCloseButton = `${this.growlDefaultDiv} .growl-close`;

    // Alert Text
    this.alertBlock = 'div.alert';
    this.alertSuccessBlock = `${this.alertBlock}.alert-success`;
    this.alertDangerBlock = `${this.alertBlock}.alert-danger`;
    this.alertInfoBlock = `${this.alertBlock}.alert-info`;
    this.alertSuccessBlockParagraph = `${this.alertSuccessBlock} div.alert-text p`;
    this.alertDangerBlockParagraph = `${this.alertDangerBlock} div.alert-text p`;
    this.alertInfoBlockParagraph = `${this.alertInfoBlock} p.alert-text`;

    // Modal dialog
    this.confirmationModal = '#confirmation_modal.show';
    this.modalDialog = `${this.confirmationModal} .modal-dialog`;
    this.modalDialogYesButton = `${this.modalDialog} button.continue`;

    // Symfony Toolbar
    this.sfToolbarMainContentDiv = "div[id*='sfToolbarMainContent']";
    this.sfCloseToolbarLink = "a[id*='sfToolbarHideButton']";

    // Sidebar
    this.rightSidebar = '#right-sidebar';
    this.helpDocumentURL = `${this.rightSidebar} div.quicknav-scroller._fullspace object`;

    // Invalid token block
    this.invalidTokenContinuelink = 'a.btn-continue';
    this.invalidTokenCancellink = 'a.btn-cancel';
  }

  /*
  Methods
   */
  /**
   * Click on link from Quick access dropdown toggle
   * @param page {Page} Browser tab
   * @param linkId {number} Page ID
   * @returns {Promise<void>}
   */
  async quickAccessToPage(page, linkId) {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.clickAndWaitForNavigation(page, this.quickAccessLink(linkId));
  }

  /**
   * Remove link from quick access
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async removeLinkFromQuickAccess(page) {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAccessRemoveLink);

    return page.textContent(this.growlDiv);
  }

  /**
   * Add current page to quick access
   * @param page {Page} Browser tab
   * @param pageName {string} Page name to add on quick access
   * @returns {Promise<string>}
   */
  async addCurrentPageToQuickAccess(page, pageName) {
    await this.dialogListener(page, true, pageName);
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAddCurrentLink);

    return page.textContent(this.growlDiv);
  }

  /**
   * Click on manage quick access link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async manageQuickAccess(page) {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.manageYourQuickAccessLink);
  }

  /**
   * Open a subMenu if closed and click on a sublink
   * @param page {Page} Browser tab
   * @param parentSelector {string} Selector of the parent menu
   * @param linkSelector {string} Selector of the child menu
   * @returns {Promise<void>}
   */
  async goToSubMenu(page, parentSelector, linkSelector) {
    if (await this.elementNotVisible(page, `${parentSelector}.open`, 1000)) {
      // open the block
      await this.scrollTo(page, parentSelector);

      await Promise.all([
        page.click(parentSelector),
        this.waitForVisibleSelector(page, `${parentSelector}.open`),
      ]);
    }
    await this.scrollTo(page, linkSelector);
    await this.clickAndWaitForNavigation(page, linkSelector);
    await this.waitForVisibleSelector(page, `${linkSelector}.link-active`);
  }

  /**
   * Returns to the dashboard then logout
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async logoutBO(page) {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.click(this.userProfileIcon);
    } else {
      await page.click(this.userProfileIconNonMigratedPages);
    }
    await this.waitForVisibleSelector(page, this.userProfileLogoutLink);
    await this.clickAndWaitForNavigation(page, this.userProfileLogoutLink);
  }

  /**
   * Close the onboarding modal if exists
   * @param page {Page} Browser tab
   * @param timeout {number} Timeout to wait for selector by milliseconds
   * @returns {Promise<void>}
   */
  async closeOnboardingModal(page, timeout = 1000) {
    if (await this.elementVisible(page, this.onboardingCloseButton, timeout)) {
      // Close popup
      await page.click(this.onboardingCloseButton);
      await this.waitForHiddenSelector(page, this.onboardingCloseButton);

      // Close menu block
      if (await this.elementVisible(page, this.onboardingStopButton, timeout)) {
        await page.click(this.onboardingStopButton);
        await this.waitForHiddenSelector(page, this.onboardingStopButton);
      }
    }
  }

  /**
   * Click on View My Shop and wait for page to open in a new Tab
   * @param page {Page} Browser tab
   * @return {Promise<Page>}
   */
  async viewMyShop(page) {
    return this.openLinkWithTargetBlank(page, this.headerShopNameLink);
  }

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param iFrameSelector {string} Selector of the iFrame to set value on
   * @param value {string} Value to set on the iFrame
   * @return {Promise<void>}
   */
  async setValueOnTinymceInput(page, iFrameSelector, value) {
    const args = {selector: iFrameSelector, vl: value};
    await page.evaluate(async (args) => {
      /* eslint-env browser */
      const iFrameElement = await document.querySelector(args.selector);
      const iFrameHtml = iFrameElement.contentDocument.documentElement;
      const textElement = await iFrameHtml.querySelector('body p');
      textElement.textContent = args.vl;
    }, args);
  }

  /**
   * Close symfony Toolbar
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeSfToolBar(page) {
    if (await this.elementVisible(page, `${this.sfToolbarMainContentDiv}[style='display: block;']`, 1000)) {
      await page.click(this.sfCloseToolbarLink);
    }
  }

  /**
   * Open help side bar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page) {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}.sidebar-open`, 2000);
  }

  /**
   * Close help side bar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page) {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}:not(.sidebar-open)`, 2000);
  }

  /**
   * Get help document URL
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getHelpDocumentURL(page) {
    return this.getAttributeContent(page, this.helpDocumentURL, 'data');
  }

  /**
   * Check if Submenu is visible
   * @param page {Page} Browser tab
   * @param parentSelector {string} Selector of the parent menu
   * @param linkSelector {string} Selector of the child menu
   * @return {Promise<boolean>}
   */
  async isSubmenuVisible(page, parentSelector, linkSelector) {
    if (await this.elementNotVisible(page, `${parentSelector}.open`, 1000)) {
      // Scroll before opening menu
      await this.scrollTo(page, parentSelector);

      await Promise.all([
        page.click(parentSelector),
        this.waitForVisibleSelector(page, `${parentSelector}.open`),
      ]);

      await this.waitForVisibleSelector(page, `${parentSelector}.open`);
    }
    return this.elementVisible(page, linkSelector, 1000);
  }

  /**
   * Get growl message content
   * @param page {Page} Browser tab
   * @param timeout {number} Timeout to wait for the selector
   * @return {Promise<string>}
   */
  getGrowlMessageContent(page, timeout = 10000) {
    return page.textContent(this.growlMessageBlock, {timeout});
  }

  /**
   * Close growl message and return its value
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeGrowlMessage(page) {
    let growlNotVisible = await this.elementNotVisible(page, this.growlMessageBlock, 10000);

    while (!growlNotVisible) {
      try {
        await page.click(this.growlCloseButton);
      } catch (e) {
        // If element does not exist it's already not visible
      }

      growlNotVisible = await this.elementNotVisible(page, this.growlMessageBlock, 2000);
    }

    await this.waitForHiddenSelector(page, this.growlMessageBlock);
  }

  /**
   * Get error message from alert danger block
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertDangerBlockParagraphContent(page) {
    return this.getTextContent(page, this.alertDangerBlockParagraph);
  }

  /**
   * Get text content of alert success block
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertSuccessBlockContent(page) {
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get text content of alert success block paragraph
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertSuccessBlockParagraphContent(page) {
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get text content of alert success block paragraph
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertInfoBlockParagraphContent(page) {
    return this.getTextContent(page, this.alertInfoBlockParagraph);
  }

  /**
   * Navigate to Bo page without token
   * @param page {Page} Browser tab
   * @param url {string} Url to BO page
   * @param continueToPage {boolean} True to continue false to cancel and return to dashboard page
   * @returns {Promise<void>}
   */
  async navigateToPageWithInvalidToken(page, url, continueToPage = true) {
    await this.goTo(page, url);
    if (await this.elementVisible(page, this.invalidTokenContinuelink, 10000)) {
      await this.clickAndWaitForNavigation(
        page,
        continueToPage ? this.invalidTokenContinuelink : this.invalidTokenCancellink,
      );
    }
  }
}

module.exports = BOBasePage;
