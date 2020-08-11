require('module-alias/register');
const CommonPage = require('@pages/commonPage');
const fs = require('fs');
const imgGen = require('js-image-generator');

module.exports = class BOBasePage extends CommonPage {
  constructor() {
    super();

    // Successful Messages
    this.successfulCreationMessage = 'Successful creation.';
    this.successfulUpdateMessage = 'Successful update.';
    this.successfulDeleteMessage = 'Successful deletion.';
    this.successfulMultiDeleteMessage = 'The selection has been successfully deleted.';

    // top navbar
    this.userProfileIconNonMigratedPages = '#employee_infos';
    this.userProfileIcon = '#header_infos #header-employee-container';
    this.userProfileLogoutLink = 'a#header_logout';
    this.shopVersionBloc = '#shop_version';
    this.headerShopNameLink = '#header_shopname';

    // Header links
    this.helpButton = '#product_form_open_help';

    // left navbar
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

    // Catalog
    this.catalogParentLink = 'li#subtab-AdminCatalog';
    // Products
    this.productsLink = '#subtab-AdminProducts';
    // Categories
    this.categoriesLink = '#subtab-AdminCategories';
    // Monitoring
    this.monitoringLink = '#subtab-AdminTracking';
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
    // Order Messages
    this.orderMessagesLink = '#subtab-AdminOrderMessage';

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
    // Link widget
    this.linkWidgetLink = '#subtab-AdminLinkWidget';

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
    // Multistore
    this.multistoreLink = '#subtab-AdminShopGroup';

    // welcome module
    this.onboardingCloseButton = 'button.onboarding-button-shut-down';
    this.onboardingStopButton = 'a.onboarding-button-stop';

    // Growls
    this.growlDefaultDiv = '#growls-default';
    this.growlMessageBlock = `${this.growlDefaultDiv} .growl-message:last-of-type`;
    this.growlCloseButton = `${this.growlDefaultDiv} .growl-close`;

    // Alert Text
    this.alertSuccessBlock = "div.alert.alert-success:not([style='display: none;'])";
    this.alertSuccessBlockParagraph = `${this.alertSuccessBlock} div.alert-text p`;
    this.alertDangerBlock = 'div.alert.alert-danger';
    this.alertDangerBlockParagraph = `${this.alertDangerBlock} div.alert-text p`;
    this.alertTextBlock = '.alert-text';

    // Alert Box
    this.alertBoxBloc = 'div.alert-box';
    this.alertBoxTextSpan = `${this.alertBoxBloc} p.alert-text span`;
    this.alertBoxButtonClose = `${this.alertBoxBloc} button.close`;

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
  }

  /*
  Methods
   */
  /**
   * Open a subMenu if closed and click on a sublink
   * @param page
   * @param parentSelector
   * @param linkSelector
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
   * @param page
   * @returns {Promise<void>}
   */
  async logoutBO(page) {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.click(this.userProfileIcon);
    } else await page.$eval(this.userProfileIconNonMigratedPages, el => el.click());
    await this.waitForVisibleSelector(page, this.userProfileLogoutLink);
    await this.clickAndWaitForNavigation(page, this.userProfileLogoutLink);
  }

  /**
   * Close the onboarding modal if exists
   * @param page
   * @returns {Promise<void>}
   */
  async closeOnboardingModal(page) {
    if (await this.elementVisible(page, this.onboardingCloseButton, 1000)) {
      await page.click(this.onboardingCloseButton);
      await this.waitForVisibleSelector(page, this.onboardingStopButton);
      await page.click(this.onboardingStopButton);
    }
  }

  /**
   * Click on View My Shop and wait for page to open in a new Tab
   * @param page
   * @return FOPage, page opened
   */
  async viewMyShop(page) {
    return this.openLinkWithTargetBlank(page, this.headerShopNameLink);
  }

  /**
   * Set value on tinyMce textarea
   * @param page
   * @param iFrameSelector
   * @param value
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
   * @param page
   * @return {Promise<void>}
   */
  async closeSfToolBar(page) {
    if (await this.elementVisible(page, `${this.sfToolbarMainContentDiv}[style='display: block;']`, 1000)) {
      await page.click(this.sfCloseToolbarLink);
    }
  }

  /**
   * Generate an image then upload it
   * @param page
   * @param selector
   * @param imageName
   * @return {Promise<void>}
   */
  async generateAndUploadImage(page, selector, imageName) {
    await imgGen.generateImage(200, 200, 1, (err, image) => {
      fs.writeFileSync(imageName, image.data);
    });
    const input = await page.$(selector);
    await input.setInputFiles(imageName);
  }

  /**
   * Delete a file from the project
   * @param page
   * @param file
   * @param wait
   * @return {Promise<void>}
   */
  async deleteFile(page, file, wait = 0) {
    fs.unlinkSync(file);
    await page.waitForTimeout(wait);
  }

  /**
   * Open help side bar
   * @param page
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page) {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}.sidebar-open`, 2000);
  }

  /**
   * Close help side bar
   * @param page
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page) {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}:not(.sidebar-open)`, 2000);
  }

  /**
   * Get help document URL
   * @param page
   * @returns {Promise<string>}
   */
  async getHelpDocumentURL(page) {
    return this.getAttributeContent(page, this.helpDocumentURL, 'data');
  }

  /**
   * Check if Submenu is visible
   * @param page
   * @param parentSelector
   * @param linkSelector
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
   * Close growl message and return its value
   * @param page
   * @return {Promise<string>}
   */
  async closeGrowlMessage(page) {
    const growlMessageText = await this.getTextContent(page, this.growlMessageBlock);
    await Promise.all([
      page.$eval(this.growlCloseButton, e => e.click()),
      page.waitForSelector(this.growlMessageBlock, {state: 'hidden'}),
    ]);
    return growlMessageText;
  }

  /**
   * Get error message from alert danger block
   * @param page
   * @return {Promise<string>}
   */
  getAlertDangerMessage(page) {
    return this.getTextContent(page, this.alertDangerBlockParagraph);
  }
};
