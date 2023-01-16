// Import pages
import CommonPage from '@pages/commonPage';

import {Page} from 'playwright';

/**
 * BO parent page, contains functions that can be used on all BO page
 * @class
 * @extends CommonPage
 */
export default class BOBasePage extends CommonPage {
  private readonly successfulCreationMessage: string;

  public readonly successfulUpdateMessage: string;

  private readonly successfulDeleteMessage: string;

  private readonly successfulMultiDeleteMessage: string;

  private readonly accessDeniedMessage: string;

  private readonly pageNotFoundMessage: string;

  private readonly userProfileIconNonMigratedPages: string;

  private readonly userProfileIcon: string;

  private readonly userProfileFirstname: string;

  private readonly userProfileAvatar: string;

  private readonly userProfileYourProfileLinkNonMigratedPages: string;

  private readonly userProfileYourProfileLink: string;

  private readonly userProfileLogoutLink: string;

  private readonly shopVersionBloc: string;

  private readonly headerShopNameLink: string;

  private readonly quickAccessDropdownToggle: string;

  private readonly quickAccessLink: (idLink: number) => string;

  private readonly quickAddCurrentLink: string;

  private readonly quickAccessRemoveLink: string;

  private readonly manageYourQuickAccessLink: string;

  private readonly navbarSarchInput: string;

  private readonly helpButton: string;

  private readonly menuMobileButton: string;

  private readonly desktopNavbar: string;

  private readonly navbarCollapseButton: string;

  private readonly navbarCollapsed: (isCollapsed: boolean) => string;

  private readonly dashboardLink: string;

  private readonly ordersParentLink: string;

  private readonly ordersLink: string;

  private readonly invoicesLink: string;

  private readonly creditSlipsLink: string;

  private readonly deliverySlipslink: string;

  private readonly shoppingCartsLink: string;

  private readonly catalogParentLink: string;

  private readonly productsLink: string;

  private readonly categoriesLink: string;

  private readonly monitoringLink: string;

  private readonly attributesAndFeaturesLink: string;

  private readonly brandsAndSuppliersLink: string;

  private readonly filesLink: string;

  private readonly discountsLink: string;

  private readonly stocksLink: string;

  public readonly customersParentLink: string;

  public readonly customersLink: string;

  private readonly addressesLink: string;

  private readonly outstandingLink: string;

  private readonly customerServiceParentLink: string;

  private readonly customerServiceLink: string;

  private readonly orderMessagesLink: string;

  private readonly merchandiseReturnsLink: string;

  private readonly modulesParentLink: string;

  private readonly moduleCatalogueLink: string;

  private readonly moduleManagerLink: string;

  private readonly designParentLink: string;

  private readonly themeAndLogoParentLink: string;

  private readonly emailThemeLink: string;

  private readonly pagesLink: string;

  private readonly positionsLink: string;

  private readonly imageSettingsLink: string;

  private readonly linkWidgetLink: string;

  private readonly shippingLink: string;

  private readonly carriersLink: string;

  private readonly shippingPreferencesLink: string;

  private readonly paymentParentLink: string;

  private readonly paymentMethodsLink: string;

  private readonly preferencesLink: string;

  private readonly internationalParentLink: string;

  private readonly taxesLink: string;

  private readonly localizationLink: string;

  private readonly locationsLink: string;

  private readonly translationsLink: string;

  private readonly shopParametersParentLink: string;

  private readonly shopParametersGeneralLink: string;

  private readonly orderSettingsLink: string;

  private readonly productSettingsLink: string;

  private readonly customerSettingsLink: string;

  private readonly contactLink: string;

  private readonly trafficAndSeoLink: string;

  private readonly searchLink: string;

  private readonly advancedParametersLink: string;

  private readonly informationLink: string;

  private readonly performanceLink: string;

  private readonly administrationLink: string;

  private readonly emailLink: string;

  private readonly importLink: string;

  private readonly teamLink: string;

  private readonly databaseLink: string;

  private readonly webserviceLink: string;

  private readonly logsLink: string;

  private readonly featureFlagLink: string;

  private readonly securityLink: string;

  private readonly multistoreLink: string;

  private readonly menuTabLink: string;

  private readonly menuTree: { parent: string; children: string[] }[];

  private readonly growlDiv: string;

  private readonly growlDefaultDiv: string;

  protected growlMessageBlock: string;

  private readonly growlCloseButton: string;

  private readonly alertBlock: string;

  private readonly alertSuccessBlock: string;

  private readonly alertDangerBlock: string;

  private readonly alertInfoBlock: string;

  protected alertSuccessBlockParagraph: string;

  private readonly alertDangerBlockParagraph: string;

  private readonly alertInfoBlockParagraph: string;

  private readonly confirmationModal: string;

  private readonly modalDialog: string;

  private readonly modalDialogYesButton: string;

  private readonly sfToolbarMainContentDiv: string;

  private readonly sfCloseToolbarLink: string;

  private readonly rightSidebar: string;

  private readonly helpDocumentURL: string;

  private readonly invalidTokenContinuelink: string;

  private readonly invalidTokenCancellink: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on all BO pages
   */
  constructor() {
    super();

    // Successful Messages
    this.successfulCreationMessage = 'Successful creation';
    this.successfulUpdateMessage = 'Successful update';
    this.successfulDeleteMessage = 'Successful deletion';
    this.successfulMultiDeleteMessage = 'The selection has been successfully deleted';

    // Access denied message
    this.accessDeniedMessage = 'Access denied';
    this.pageNotFoundMessage = 'Page not found';

    // top navbar
    this.userProfileIconNonMigratedPages = '#employee_infos';
    this.userProfileIcon = '#header_infos #header-employee-container';
    this.userProfileFirstname = '.employee-wrapper-avatar .employee_profile';
    this.userProfileAvatar = '.employee-avatar img';
    this.userProfileYourProfileLinkNonMigratedPages = '.employee-wrapper-profile > a.admin-link';
    this.userProfileYourProfileLink = '.employee-link.profile-link';
    this.userProfileLogoutLink = 'a#header_logout';
    this.shopVersionBloc = '#shop_version';
    this.headerShopNameLink = '#header_shopname';
    this.quickAccessDropdownToggle = '#quick_select';
    this.quickAccessLink = (idLink) => `.quick-row-link:nth-child(${idLink})`;
    this.quickAddCurrentLink = '#quick-add-link';
    this.quickAccessRemoveLink = '#quick-remove-link';
    this.manageYourQuickAccessLink = '#quick-manage-link';
    this.navbarSarchInput = '#bo_query';

    // Header links
    this.helpButton = '#product_form_open_help';
    this.menuMobileButton = '.js-mobile-menu';

    // left navbar
    this.desktopNavbar = '.nav-bar:not(.mobile-nav)';
    this.navbarCollapseButton = '.nav-bar > .menu-collapse';
    this.navbarCollapsed = (isCollapsed) => `body${isCollapsed
      ? '.page-sidebar-closed'
      : ':not(.page-sidebar-closed)'}`;

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
    this.outstandingLink = '#subtab-AdminOutstanding';

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
    // Theme & Logo
    this.themeAndLogoParentLink = '#subtab-AdminThemesParent';
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
    this.paymentMethodsLink = '#subtab-AdminPayment';
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
    // Information
    this.informationLink = '#subtab-AdminInformation';
    // Performance
    this.performanceLink = '#subtab-AdminPerformance';
    // Administration
    this.administrationLink = '#subtab-AdminAdminPreferences';
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
    // New & Experimental Features
    this.featureFlagLink = '#subtab-AdminFeatureFlag';
    // Security
    this.securityLink = '#subtab-AdminParentSecurity';
    // Multistore
    this.multistoreLink = '#subtab-AdminShopGroup';
    // Deprecated tab used for regression test
    this.menuTabLink = '#subtab-AdminTabs';

    this.menuTree = [
      {
        parent: this.ordersParentLink,
        children: [
          this.ordersLink,
          this.invoicesLink,
          this.creditSlipsLink,
          this.deliverySlipslink,
          this.shoppingCartsLink,
        ],
      },
      {
        parent: this.customersParentLink,
        children: [
          this.customersLink,
          this.addressesLink,
        ],
      },
      {
        parent: this.customerServiceParentLink,
        children: [
          this.customerServiceLink,
          this.orderMessagesLink,
          this.merchandiseReturnsLink,
        ],
      },
      {
        parent: this.modulesParentLink,
        children: [
          this.moduleManagerLink,
        ],
      },
      {
        parent: this.designParentLink,
        children: [
          this.themeAndLogoParentLink,
          this.emailThemeLink,
          this.pagesLink,
          this.positionsLink,
          this.imageSettingsLink,
          this.linkWidgetLink,
        ],
      },
      {
        parent: this.shippingLink,
        children: [
          this.carriersLink,
          this.shippingPreferencesLink,
        ],
      },
      {
        parent: this.paymentParentLink,
        children: [
          this.paymentMethodsLink,
          this.preferencesLink,
        ],
      },
      {
        parent: this.internationalParentLink,
        children: [
          this.localizationLink,
          this.locationsLink,
          this.taxesLink,
          this.translationsLink,
        ],
      },
      {
        parent: this.shopParametersParentLink,
        children: [
          this.shopParametersGeneralLink,
          this.orderSettingsLink,
          this.productSettingsLink,
          this.customerSettingsLink,
          this.contactLink,
          this.trafficAndSeoLink,
          this.searchLink,
        ],
      },
      {
        parent: this.advancedParametersLink,
        children: [
          this.informationLink,
          this.performanceLink,
          this.administrationLink,
          this.emailLink,
          this.importLink,
          this.teamLink,
          this.databaseLink,
          this.logsLink,
          this.webserviceLink,
          this.featureFlagLink,
          this.securityLink,
        ],
      },
    ];

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
  async quickAccessToPage(page: Page, linkId: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.clickAndWaitForNavigation(page, this.quickAccessLink(linkId));
    await this.waitForPageTitleToLoad(page);
  }

  /**
   * Click on link from Quick access dropdown toggle and get the opened Page
   * @param page {Page} Browser tab
   * @param linkId {number} Page ID
   * @returns {Promise<Page>}
   */
  async quickAccessToPageNewWindow(page: Page, linkId: number): Promise<Page> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    return this.openLinkWithTargetBlank(page, this.quickAccessLink(linkId));
  }

  /**
   * Remove link from quick access
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async removeLinkFromQuickAccess(page: Page): Promise<string|null> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAccessRemoveLink);

    return page.textContent(this.growlDiv);
  }

  /**
   * Add current page to quick access
   * @param page {Page} Browser tab
   * @param pageName {string} Page name to add on quick access
   * @returns {Promise<string|null>}
   */
  async addCurrentPageToQuickAccess(page: Page, pageName: string): Promise<string|null> {
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
  async goToManageQuickAccessPage(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.clickAndWaitForNavigation(page, this.manageYourQuickAccessLink);
  }

  /**
   * Open a subMenu if closed and click on a sublink
   * @param page {Page} Browser tab
   * @param parentSelector {string} Selector of the parent menu
   * @param linkSelector {string} Selector of the child menu
   * @returns {Promise<void>}
   */
  async goToSubMenu(page: Page, parentSelector: string, linkSelector: string): Promise<void> {
    await this.clickSubMenu(page, parentSelector);
    await this.scrollTo(page, linkSelector);
    await this.clickAndWaitForNavigation(page, linkSelector);
    if (await this.isSidebarCollapsed(page)) {
      await this.waitForHiddenSelector(page, `${linkSelector}.link-active`);
    } else {
      await this.waitForVisibleSelector(page, `${linkSelector}.link-active`);
    }
  }

  /**
   * Open a subMenu
   * @param page {Page} Browser tab
   * @param parentSelector {string} Selector of the parent menu
   * @returns {Promise<void>}
   */
  async clickSubMenu(page: Page, parentSelector: string): Promise<void> {
    const openSelector = await this.isSidebarCollapsed(page) ? '.ul-open' : '.open';

    if (await this.elementNotVisible(page, `${parentSelector}${openSelector}`, 1000)) {
      // open the block
      await this.scrollTo(page, parentSelector);

      await Promise.all([
        page.click(parentSelector),
        this.waitForVisibleSelector(page, `${parentSelector}${openSelector}`),
      ]);
    }
  }

  /**
   * Return is a submenu is active
   * @param page {Page} Browser tab
   * @param linkSelector {string} Selector of the menu
   * @return {Promise<boolean>}
   */
  async isSubMenuActive(page: Page, linkSelector: string): Promise<boolean> {
    return (await page.$$(`${linkSelector}.link-active`)).length > 0;
  }

  /**
   * Return is the navbar is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isNavbarVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.desktopNavbar, 1000);
  }

  /**
   * Return is the navbar is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isMobileMenuVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.menuMobileButton, 1000);
  }

  /**
   * Returns if Submenu is visible
   * @param page {Page} Browser tab
   * @param parentSelector {string} Selector of the parent menu
   * @param linkSelector {string} Selector of the child menu
   * @return {Promise<boolean>}
   */
  async isSubmenuVisible(page: Page, parentSelector: string, linkSelector: string): Promise<boolean> {
    const openSelector = await this.isSidebarCollapsed(page) ? '.ul-open' : '.open';

    if (await this.elementNotVisible(page, `${parentSelector}${openSelector}`, 1000)) {
      // Scroll before opening menu
      await this.scrollTo(page, parentSelector);

      await Promise.all([
        page.click(parentSelector),
        this.waitForVisibleSelector(page, `${parentSelector}${openSelector}`),
      ]);

      await this.waitForVisibleSelector(page, `${parentSelector}${openSelector}`);
    }
    return this.elementVisible(page, linkSelector, 1000);
  }

  /**
   * Collapse the sidebar
   * @param page {Page} Browser tab
   * @param isCollapsed {boolean} Selector of the parent menu
   * @return {Promise<void>}
   */
  async setSidebarCollapsed(page: Page, isCollapsed: boolean): Promise<void> {
    const isCurrentCollapsed = await this.isSidebarCollapsed(page);

    if (isCurrentCollapsed !== isCollapsed) {
      await Promise.all([
        page.click(this.navbarCollapseButton),
        this.waitForVisibleSelector(
          page,
          this.navbarCollapsed(isCollapsed),
        ),
      ]);
    }
  }

  /**
   * Returns if the sidebar is collapsed
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isSidebarCollapsed(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.navbarCollapsed(true), 1000);
  }

  /**
   * Returns to the dashboard then logout
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMyProfile(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.click(this.userProfileIcon);
    } else {
      await page.click(this.userProfileIconNonMigratedPages);
    }
    if (await this.elementVisible(page, this.userProfileYourProfileLink, 1000)) {
      await this.waitForVisibleSelector(page, this.userProfileYourProfileLink);
    } else {
      await this.waitForVisibleSelector(page, this.userProfileYourProfileLinkNonMigratedPages);
    }
    await this.clickAndWaitForNavigation(page, this.userProfileYourProfileLink);
  }

  /**
   * Returns the URL of the avatar for the current employee from the dropdown
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getCurrentEmployeeAvatar(page: Page): Promise<string|null> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.click(this.userProfileIcon);
    } else {
      await page.click(this.userProfileIconNonMigratedPages);
    }

    return page.getAttribute(this.userProfileAvatar, 'src');
  }

  /**
   * Returns to the dashboard then logout
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async logoutBO(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.click(this.userProfileIcon);
    } else {
      await page.click(this.userProfileIconNonMigratedPages);
    }
    await this.waitForVisibleSelector(page, this.userProfileLogoutLink);
    await this.clickAndWaitForNavigation(page, this.userProfileLogoutLink);
  }

  /**
   * Click on View My Shop and wait for page to open in a new Tab
   * @param page {Page} Browser tab
   * @return {Promise<Page>}
   */
  async viewMyShop(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.headerShopNameLink);
  }

  /**
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param iFrameSelector {string} Selector of the iFrame to set value on
   * @param value {string} Value to set on the iFrame
   * @return {Promise<void>}
   */
  async setValueOnTinymceInput(page: Page, iFrameSelector: string, value: string): Promise<void> {
    const args = {selector: iFrameSelector, vl: value};
    // eslint-disable-next-line no-eval
    const fn = eval(`({
      async fnSetValueOnTinymceInput(args) {
        /* eslint-env browser */
        const iFrameElement = await document.querySelector(args.selector);
        const iFrameHtml = iFrameElement.contentDocument.documentElement;
        const textElement = await iFrameHtml.querySelector('body p');
        textElement.textContent = args.vl;
      }
    })`);
    await page.evaluate(fn.fnSetValueOnTinymceInput, args);
  }

  /**
   * Close symfony Toolbar
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeSfToolBar(page: Page): Promise<void> {
    if (await this.elementVisible(page, `${this.sfToolbarMainContentDiv}[style='display: block;']`, 1000)) {
      await page.click(this.sfCloseToolbarLink);
    }
  }

  /**
   * Open help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openHelpSideBar(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}.sidebar-open`, 2000);
  }

  /**
   * Close help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeHelpSideBar(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.helpButton);
    return this.elementVisible(page, `${this.rightSidebar}:not(.sidebar-open)`, 2000);
  }

  /**
   * Get help document URL
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getHelpDocumentURL(page: Page): Promise<string|null> {
    return this.getAttributeContent(page, this.helpDocumentURL, 'data');
  }

  /**
   * Get growl message content
   * @param page {Page} Browser tab
   * @param timeout {number} Timeout to wait for the selector
   * @return {Promise<string|null>}
   */
  getGrowlMessageContent(page: Page, timeout: number = 10000): Promise<string|null> {
    return page.textContent(this.growlMessageBlock, {timeout});
  }

  /**
   * Close growl message and return its value
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeGrowlMessage(page: Page): Promise<void> {
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
  getAlertDangerBlockParagraphContent(page: Page): Promise<string|null> {
    return this.getTextContent(page, this.alertDangerBlockParagraph);
  }

  /**
   * Get text content of alert success block
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertSuccessBlockContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get text content of alert success block paragraph
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertSuccessBlockParagraphContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get text content of alert block paragraph
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getAlertInfoBlockParagraphContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertInfoBlockParagraph);
  }

  /**
   * Navigate to Bo page without token
   * @param page {Page} Browser tab
   * @param url {string} Url to BO page
   * @param continueToPage {boolean} True to continue false to cancel and return to dashboard page
   * @returns {Promise<void>}
   */
  async navigateToPageWithInvalidToken(page: Page, url: string, continueToPage: boolean = true): Promise<void> {
    await this.goTo(page, url);
    if (await this.elementVisible(page, this.invalidTokenContinuelink, 10000)) {
      await this.clickAndWaitForNavigation(
        page,
        continueToPage ? this.invalidTokenContinuelink : this.invalidTokenCancellink,
      );
    }
  }

  /**
   * Search in BackOffice
   * @param page {Page} Browser tab
   * @param query {string} String
   * @returns {Promise<void>}
   */
  async search(page: Page, query: string): Promise<void> {
    await this.setValue(page, this.navbarSarchInput, query);
    await page.keyboard.press('Enter');
    await page.waitForNavigation({waitUntil: 'networkidle'});
  }

  /**
   * Resize the page to defined viewport
   * @param page {Page} Browser tab
   * @param mobileSize {boolean} Define if the viewport is for mobile or not
   * @returns {Promise<void>}
   */
  async resize(page: Page, mobileSize: boolean): Promise<void> {
    await super.resize(page, mobileSize);
    await this.waitForSelector(page, this.menuMobileButton, mobileSize ? 'visible' : 'hidden');
  }
}

module.exports = BOBasePage;
