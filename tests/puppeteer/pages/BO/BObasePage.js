require('module-alias/register');
const CommonPage = require('@pages/commonPage');
const fs = require('fs');
const imgGen = require('js-image-generator');

module.exports = class BOBasePage extends CommonPage {
  constructor(page) {
    super(page);

    // Successful Messages
    this.successfulCreationMessage = 'Successful creation.';
    this.successfulUpdateMessage = 'Successful update.';
    this.successfulDeleteMessage = 'Successful deletion.';
    this.successfulMultiDeleteMessage = 'The selection has been successfully deleted.';

    // top navbar
    this.headerLogoImage = '#header_logo';
    this.userProfileIcon = '#employee_infos,#header_infos #header-employee-container';
    this.userProfileLogoutLink = 'a#header_logout';
    this.shopVersionBloc = '#shop_version';
    this.headerShopNameLink = '#header_shopname';

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
    // Stocks
    this.stocksLink = '#subtab-AdminStockManagement';

    // Customers
    this.customersParentLink = 'li#subtab-AdminParentCustomer';
    this.customersLink = '#subtab-AdminCustomers';

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
    this.emailThemeLink = '#subtab-AdminMailThemeParent';
    // Pages
    this.pagesLink = '#subtab-AdminCmsContent';
    // Link widget
    this.linkWidgetLink = '#subtab-AdminLinkWidget';

    // International
    this.internationalParentLink = '#subtab-AdminInternational';
    // Taxes
    this.taxesLink = '#subtab-AdminParentTaxes';
    // Localization
    this.localizationLink = '#subtab-AdminParentLocalization';

    // Shop Parameters
    this.shopParametersParentLink = '#subtab-ShopParameters';
    // General
    this.shopParametersGeneralLink = '#subtab-AdminParentPreferences';
    // Contact
    this.contactLink = '#subtab-AdminParentStores';
    // traffic and SEO
    this.trafficAndSeoLink = '#subtab-AdminParentMeta';

    // Advanced Parameters
    this.advancedParametersLink = '#subtab-AdminAdvancedParameters';
    // Team
    this.teamLink = '#subtab-AdminParentEmployees';
    // Database
    this.databaseLink = '#subtab-AdminParentRequestSql';

    // welcome module
    this.onboardingCloseButton = 'button.onboarding-button-shut-down';
    this.onboardingStopButton = 'a.onboarding-button-stop';

    // Growls
    this.growlMessageBloc = '#growls .growl-message';
    this.growlDefaultMessageBloc = '#growls-default .growl-message';

    // Alert Text
    this.alertSuccessBloc = 'div.alert.alert-success:not([style=\'display: none;\'])';
    this.alertSuccessBlockParagraph = `${this.alertSuccessBloc} div.alert-text p`;
    this.alertTextBlock = '.alert-text';

    // Alert Box
    this.alertBoxBloc = 'div.alert-box';
    this.alertBoxTextSpan = `${this.alertBoxBloc} p.alert-text span`;
    this.alertBoxButtonClose = `${this.alertBoxBloc} button.close`;

    // Modal dialog
    this.modalDialog = '#confirmation_modal.show .modal-dialog';
    this.modalDialogYesButton = `${this.modalDialog} button.continue`;
    this.modalDialogNoButton = `${this.modalDialog} button.cancel`;

    // Symfony Toolbar
    this.sfToolbarMainContentDiv = 'div[id*=\'sfToolbarMainContent\']';
    this.sfCloseToolbarLink = 'a[id*=\'sfToolbarHideButton\']';
  }

  /*
  Methods
   */
  /**
   * Open a subMenu if closed and click on a sublink
   * @param parentSelector
   * @param linkSelector
   * @returns {Promise<void>}
   */
  async goToSubMenu(parentSelector, linkSelector) {
    if (!(await this.elementNotVisible(`${parentSelector}.open`, 1000))) {
      await this.clickAndWaitForNavigation(linkSelector);
    } else {
      // open the block
      await Promise.all([
        this.page.click(parentSelector),
        this.page.waitForSelector(`${parentSelector}.open`, {visible: true}),
      ]);
      await this.clickAndWaitForNavigation(linkSelector);
    }
    await this.page.waitForSelector(`${linkSelector}.-active`, {visible: true});
  }

  /**
   * Returns to the dashboard then logout
   * @returns {Promise<void>}
   */
  async logoutBO() {
    await this.clickAndWaitForNavigation(this.headerLogoImage);
    await this.page.waitForSelector(this.userProfileIcon, {visible: true});
    await this.page.click(this.userProfileIcon);
    await this.page.waitForSelector(this.userProfileLogoutLink, {visible: true});
    await this.clickAndWaitForNavigation(this.userProfileLogoutLink);
  }

  /**
   * Close the onboarding modal if exists
   * @returns {Promise<void>}
   */
  async closeOnboardingModal() {
    if (await this.elementVisible(this.onboardingCloseButton, 1000)) {
      await this.page.click(this.onboardingCloseButton);
      await this.page.waitForSelector(this.onboardingStopButton, {visible: true});
      await this.page.click(this.onboardingStopButton);
    }
  }

  /**
   * Click on View My Shop and wait for page to open in a new Tab
   * @return FOPage, page opened
   */
  async viewMyShop() {
    return this.openLinkWithTargetBlank(this.page, this.headerShopNameLink, false);
  }

  /**
   * Set value on tinyMce textareas
   * @param iFrameSelector
   * @param value
   * @return {Promise<void>}
   */
  async setValueOnTinymceInput(iFrameSelector, value) {
    await this.page.click(iFrameSelector, {clickCount: 3});
    await this.page.keyboard.type(value);
  }

  /**
   * Close symfony Toolbar
   * @return {Promise<void>}
   */
  async closeSfToolBar() {
    if (await this.elementVisible(`${this.sfToolbarMainContentDiv}[style='display: block;']`, 1000)) {
      await this.page.click(this.sfCloseToolbarLink);
    }
  }

  /**
   * Generate an image then upload it
   * @param selector
   * @param imageName
   * @return {Promise<void>}
   */
  async generateAndUploadImage(selector, imageName) {
    await imgGen.generateImage(200, 200, 1, (err, image) => {
      fs.writeFileSync(imageName, image.data);
    });
    const input = await this.page.$(selector);
    await input.uploadFile(imageName);
  }

  /**
   * Delete a file from the project
   * @param file
   * @param wait
   * @return {Promise<void>}
   */
  async deleteFile(file, wait = 0) {
    fs.unlinkSync(file);
    await this.page.waitFor(wait);
  }
};
