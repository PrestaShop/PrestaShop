// Import pages
import CommonPage from '@pages/commonPage';

import {Frame, Page} from 'playwright';
import type {PageFunction} from 'playwright-core/types/structs';

/**
 * BO parent page, contains functions that can be used on all BO page
 * @class
 * @extends CommonPage
 */
export default class BOBasePage extends CommonPage {
  public successfulCreationMessage: string;

  public successfulUpdateMessage: string;

  public successfulDeleteMessage: string;

  public successfulMultiDeleteMessage: string;

  public readonly accessDeniedMessage: string;

  public readonly pageNotFoundMessage: string;

  private readonly userProfileIconNonMigratedPages: string;

  protected readonly userProfileIcon: string;

  private readonly userProfileFirstname: string;

  private readonly userProfileAvatar: string;

  private readonly userProfileYourProfileLinkNonMigratedPages: string;

  private readonly userProfileYourProfileLink: string;

  private readonly userProfileLogoutLink: string;

  private readonly shopVersionBloc: string;

  private readonly headerShopNameLink: string;

  private readonly quickAccessContainer: string;

  private readonly quickAccessDropdownToggle: string;

  private readonly quickAccessLink: (linkName: string) => string;

  private readonly quickAddCurrentLink: string;

  private readonly quickAccessRemoveLink: string;

  private readonly manageYourQuickAccessLink: string;

  private readonly navbarSearchInput: string;

  protected readonly helpButton: string;

  private readonly menuMobileButton: string;

  private readonly notificationsLink: string;

  private readonly notificationsDropDownMenu: string;

  private readonly totalNotificationsValue: string;

  private readonly notificationsTab: (tabName: string) => string;

  private readonly notificationsNumberInTab: (tabName: string) => string;

  private readonly notificationRowInTab: (tabName: string, row: number) => string;

  private readonly desktopNavbar: string;

  private readonly navbarCollapseButton: string;

  private readonly navbarCollapsed: (isCollapsed: boolean) => string;

  private readonly dashboardLink: string;

  public readonly ordersParentLink: string;

  public readonly ordersLink: string;

  public readonly invoicesLink: string;

  public readonly creditSlipsLink: string;

  public readonly deliverySlipslink: string;

  public readonly shoppingCartsLink: string;

  public readonly catalogParentLink: string;

  public readonly productsLink: string;

  public readonly categoriesLink: string;

  public readonly monitoringLink: string;

  public readonly attributesAndFeaturesLink: string;

  public readonly brandsAndSuppliersLink: string;

  public readonly filesLink: string;

  public readonly discountsLink: string;

  public readonly stocksLink: string;

  public readonly customersParentLink: string;

  public readonly customersLink: string;

  public readonly addressesLink: string;

  public readonly outstandingLink: string;

  public readonly customerServiceParentLink: string;

  public readonly customerServiceLink: string;

  public readonly orderMessagesLink: string;

  public readonly merchandiseReturnsLink: string;

  public readonly modulesParentLink: string;

  public readonly moduleCatalogueLink: string;

  public readonly moduleManagerLink: string;

  public readonly designParentLink: string;

  public readonly themeAndLogoParentLink: string;

  public readonly themeAndLogoLink: string;

  public readonly emailThemeLink: string;

  public readonly pagesLink: string;

  public readonly positionsLink: string;

  public readonly imageSettingsLink: string;

  public readonly linkWidgetLink: string;

  public readonly shippingLink: string;

  public readonly carriersLink: string;

  public readonly shippingPreferencesLink: string;

  public readonly paymentParentLink: string;

  private readonly paymentMethodsLink: string;

  public readonly preferencesLink: string;

  public readonly internationalParentLink: string;

  public readonly taxesLink: string;

  public readonly localizationLink: string;

  public readonly locationsLink: string;

  public readonly translationsLink: string;

  public readonly shopParametersParentLink: string;

  public readonly shopParametersGeneralLink: string;

  public readonly orderSettingsLink: string;

  public readonly productSettingsLink: string;

  public readonly customerSettingsLink: string;

  public readonly contactLink: string;

  public readonly trafficAndSeoLink: string;

  public readonly searchLink: string;

  public readonly advancedParametersLink: string;

  private readonly informationLink: string;

  public readonly performanceLink: string;

  public readonly administrationLink: string;

  public readonly emailLink: string;

  public readonly importLink: string;

  public readonly teamLink: string;

  public readonly databaseLink: string;

  public readonly webserviceLink: string;

  public readonly logsLink: string;

  public readonly authorizationServerLink: string;

  public readonly featureFlagLink: string;

  private readonly securityLink: string;

  public readonly multistoreLink: string;

  public readonly menuTabLink: string;

  public readonly menuTree: { parent: string; children: string[] }[];

  protected readonly growlDiv: string;

  private readonly growlDefaultDiv: string;

  protected growlMessageBlock: string;

  protected growlCloseButton: string;

  protected alertBlock: string;

  protected alertTextBlock: string;

  protected alertBlockCloseButton: string;

  protected alertSuccessBlock: string;

  private readonly alertDangerBlock: string;

  private readonly alertInfoBlock: string;

  protected alertSuccessBlockParagraph: string;

  protected alertDangerBlockParagraph: string;

  private readonly alertInfoBlockParagraph: string;

  private readonly confirmationModal: string;

  protected readonly modalDialog: string;

  protected readonly modalDialogYesButton: string;

  private readonly sfToolbarMainContentDiv: string;

  private readonly sfCloseToolbarLink: string;

  protected readonly rightSidebar: string;

  private readonly helpDocumentURL: string;

  private readonly invalidTokenContinueLink: string;

  private readonly invalidTokenCancelLink: string;

  public readonly debugModeToolbar: string;

  public readonly multistoreHeader: string;

  public readonly multistoreButton: string;

  public readonly multistoreModal: string;

  public readonly viewMyStoreButton: string;

  public readonly multistoreTopBar: string;

  public readonly storeName: string;

  public readonly pageSubtitle: string;

  public readonly chooseShopName: (shopNumber: number) => string;

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
    this.successfulMultiDeleteMessage = 'The selection has been successfully deleted.';

    // Access denied message
    this.accessDeniedMessage = 'Access denied';
    this.pageNotFoundMessage = 'Page not found';

    this.pageSubtitle = '#content .page-subtitle';

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
    this.quickAccessContainer = '#quick-access-container';
    this.quickAccessLink = (linkName) => `${this.quickAccessContainer} [data-item='${linkName}']`;
    this.quickAddCurrentLink = `${this.quickAccessContainer} #quick-add-link`;
    this.quickAccessRemoveLink = `${this.quickAccessContainer} #quick-remove-link`;
    this.manageYourQuickAccessLink = `${this.quickAccessContainer} #quick-manage-link`;
    this.navbarSearchInput = '#bo_query';

    // Header links
    this.helpButton = '#product_form_open_help';
    this.menuMobileButton = '.js-mobile-menu';
    this.notificationsLink = '#notification,#notif';
    this.notificationsDropDownMenu = '#notification div.dropdown-menu-right.notifs_dropdown,#notif div.dropdown-menu';
    this.totalNotificationsValue = '#total_notif_value,#notifications-total';
    this.notificationsTab = (tabName: string) => `#${tabName}-tab`;
    this.notificationsNumberInTab = (tabName: string) => `#${tabName}_notif_value,#_nb_new_${tabName}_`;
    this.notificationRowInTab = (tabName: string, row: number) => `#${tabName}-notifications div a:nth-child(${row})`;

    // left navbar
    this.desktopNavbar = '.nav-bar:not(.mobile-nav)';
    this.navbarCollapseButton = '.nav-bar > .menu-collapse';
    this.navbarCollapsed = (isCollapsed) => `body${isCollapsed
      ? '.page-sidebar-closed'
      : ':not(.page-sidebar-closed)'}`;

    this.debugModeToolbar = 'div[id*=sfToolbarMainContent]';

    // Multistore selectors
    this.multistoreHeader = '#header-multishop';
    this.multistoreButton = `${this.multistoreHeader} button.header-multishop-button`;
    this.multistoreModal = '#multishop-modal';
    this.chooseShopName = (shopNumber: number) => `${this.multistoreModal} li:nth-child(${2 + shopNumber})`
      + ' a.multishop-modal-shop-name';
    this.viewMyStoreButton = `${this.multistoreHeader} div.header-multishop-right a.header-multishop-view-action`;
    this.multistoreTopBar = `${this.multistoreHeader} div.header-multishop-top-bar`;
    this.storeName = `${this.multistoreTopBar} div h2`;

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
    this.themeAndLogoLink = '#subtab-AdminThemes';
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
    // Authorization Server
    this.authorizationServerLink = '#subtab-AdminAuthorizationServer';
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
    this.alertTextBlock = `${this.alertBlock} div.alert-text`;
    this.alertBlockCloseButton = `${this.alertBlock} button[aria-label='Close']`;
    this.alertSuccessBlock = `${this.alertBlock}.alert-success`;
    this.alertDangerBlock = `${this.alertBlock}.alert-danger`;
    this.alertInfoBlock = `${this.alertBlock}.alert-info`;
    this.alertSuccessBlockParagraph = `${this.alertSuccessBlock} div.alert-text p`;
    this.alertDangerBlockParagraph = `${this.alertDangerBlock} div.alert-text p`;
    this.alertInfoBlockParagraph = `${this.alertInfoBlock} div.alert-text, ${this.alertInfoBlock} p.alert-text`;

    // Modal dialog
    this.confirmationModal = '#confirmation_modal.show';
    this.modalDialog = `${this.confirmationModal} .modal-dialog`;
    this.modalDialogYesButton = `${this.modalDialog} button.continue`;

    // Symfony Toolbar
    this.sfToolbarMainContentDiv = "div[id*='sfToolbarMainContent']";
    this.sfCloseToolbarLink = "button[id*='sfToolbarHideButton']";

    // Sidebar
    this.rightSidebar = '#right-sidebar';
    this.helpDocumentURL = `${this.rightSidebar} div.quicknav-scroller._fullspace object`;

    // Invalid token block
    this.invalidTokenContinueLink = 'a.btn-continue';
    this.invalidTokenCancelLink = 'a.btn-cancel';
  }

  /*
  Methods
   */
  /**
   * Get page subtitle
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPageSubTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.pageSubtitle);
  }

  /**
   * Go to dashboard page
   * @param page {Page} Browser tab
   */
  async goToDashboardPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.dashboardLink);
  }

  /**
   * Click on link from Quick access dropdown toggle
   * @param page {Page} Browser tab
   * @param linkName {linkName} Page name
   * @returns {Promise<void>}
   */
  async quickAccessToPage(page: Page, linkName: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.clickAndWaitForURL(page, this.quickAccessLink(linkName));
    await this.waitForPageTitleToLoad(page);
  }

  /**
   * Quick access to page with frame
   * @param page {Page} Browser tab
   * @param linkName {linkName} Page name
   * @returns {Promise<Page>}
   */
  async quickAccessToPageWithFrame(page: Page, linkName: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAccessLink(linkName));
  }

  /**
   * Click on link from Quick access dropdown toggle and get the opened Page
   * @param page {Page} Browser tab
   * @param linkName {linkName} Page name
   * @returns {Promise<Page>}
   */
  async quickAccessToPageNewWindow(page: Page, linkName: string): Promise<Page> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    return this.openLinkWithTargetBlank(page, this.quickAccessLink(linkName));
  }

  /**
   * Remove link from quick access
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async removeLinkFromQuickAccess(page: Page): Promise<string | null> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAccessRemoveLink);

    return page.locator(this.growlDiv).textContent();
  }

  /**
   * Add current page to quick access
   * @param page {Page} Browser tab
   * @param pageName {string} Page name to add on quick access
   * @returns {Promise<string|null>}
   */
  async addCurrentPageToQuickAccess(page: Page, pageName: string): Promise<string | null> {
    await this.dialogListener(page, true, pageName);
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.waitForSelectorAndClick(page, this.quickAddCurrentLink);

    return page.locator(this.growlDiv).textContent();
  }

  /**
   * Click on manage quick access link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToManageQuickAccessPage(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.quickAccessDropdownToggle);
    await this.clickAndWaitForURL(page, this.manageYourQuickAccessLink);
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
    await this.clickAndWaitForURL(page, linkSelector);
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
        page.locator(parentSelector).click(),
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
    return ((await page.locator(`${linkSelector}.link-active`).count()) > 0);
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
        page.locator(parentSelector).click(),
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
        page.locator(this.navbarCollapseButton).click(),
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
   * Is notifications link visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isNotificationsLinkVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.notificationsLink, 1000);
  }

  /**
   * Click on notifications link
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async clickOnNotificationsLink(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.notificationsLink);

    return this.elementVisible(page, this.notificationsDropDownMenu, 1000);
  }

  /**
   * Get all notifications number
   * @param page {Page} Browser tab
   * @return {Promise<number>}
   */
  async getAllNotificationsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.totalNotificationsValue, 2000);
  }

  /**
   * Is notifications tab visible
   * @param page {Page} Browser tab
   * @param tabName {string} Messages, customers or orders tab
   * @return {Promise<boolean>}
   */
  async isNotificationsTabVisible(page: Page, tabName: string): Promise<boolean> {
    return this.elementVisible(page, this.notificationsTab(tabName));
  }

  /**
   * Click on notifications tab
   * @param page {Page} Browser tab
   * @param tabName {string} Messages, customers or orders tab
   * @return {Promise<void>}
   */
  async clickOnNotificationsTab(page: Page, tabName: string): Promise<void> {
    await this.waitForSelectorAndClick(page, this.notificationsTab(tabName));
  }

  /**
   * Get notifications number in tab
   * @param page {Page} Browser tab
   * @param tabName {string} Messages, customers or orders tab
   * @return {Promise<number>}
   */
  async getNotificationsNumberInTab(page: Page, tabName: string): Promise<number> {
    return this.getNumberFromText(page, this.notificationsNumberInTab(tabName), 2000);
  }

  /**
   * Click on notification on tab
   * @param page {Page} Browser tab
   * @param tabName {string} Messages, customers or orders tab
   * @param row {number} row in notification tab
   */
  async clickOnNotification(page: Page, tabName: string, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.notificationRowInTab(tabName, row));
  }

  /**
   * Go to my profile page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   * @return {Promise<void>}
   */
  async goToMyProfile(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.locator(this.userProfileIcon).click();
    } else {
      await page.locator(this.userProfileIconNonMigratedPages).click();
    }
    if (await this.elementVisible(page, this.userProfileYourProfileLink, 1000)) {
      await this.waitForVisibleSelector(page, this.userProfileYourProfileLink);
    } else {
      await this.waitForVisibleSelector(page, this.userProfileYourProfileLinkNonMigratedPages);
    }
    await this.clickAndWaitForURL(page, this.userProfileYourProfileLink);
  }

  /**
   * Returns the URL of the avatar for the current employee from the dropdown
   * @param page {Page} Browser tab
   * @returns {Promise<string|null>}
   */
  async getCurrentEmployeeAvatar(page: Page): Promise<string | null> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.locator(this.userProfileIcon).click();
    } else {
      await page.locator(this.userProfileIconNonMigratedPages).click();
    }

    return this.getAttributeContent(page, this.userProfileAvatar, 'src');
  }

  /**
   * Returns to the dashboard then logout
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async logoutBO(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.userProfileIcon, 1000)) {
      await page.locator(this.userProfileIcon).click();
    } else {
      await page.locator(this.userProfileIconNonMigratedPages).click();
    }
    await this.waitForVisibleSelector(page, this.userProfileLogoutLink);
    await this.clickAndWaitForURL(page, this.userProfileLogoutLink);
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
    const fn: { fnSetValueOnTinymceInput: PageFunction<{ selector: string, vl: string }, void> } = eval(`({
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
   * Set value on tinyMce textarea
   * @param page {Page} Browser tab
   * @param selector {string} Selector of the input to set value on
   * @param value {string} Value
   * @param onChange {boolean} Trigger the event 'change' on selector
   * @return {Promise<void>}
   */
  async setValueOnDateTimePickerInput(page: Page, selector: string, value: string, onChange: boolean = false): Promise<void> {
    const args = {selector, value, onChange};
    // eslint-disable-next-line no-eval
    const fn: { fnSetValueOnDTPickerInput: PageFunction<{ selector: string, value: string, onChange: boolean }, void> } = eval(`({
      async fnSetValueOnDTPickerInput(args) {
        /* eslint-env browser */
        const textElement = await document.querySelector(args.selector);
        textElement.value = args.value;
        if (args.onChange) {
          textElement.dispatchEvent(new Event('change'));
        }
      }
    })`);
    await page.evaluate(fn.fnSetValueOnDTPickerInput, args);
  }

  /**
   * Close symfony Toolbar
   * @param page {Frame|Page} Browser tab
   * @return {Promise<void>}
   */
  async closeSfToolBar(page: Frame | Page): Promise<void> {
    if (await this.elementVisible(page, `${this.sfToolbarMainContentDiv}[style='display: block;']`, 1000)) {
      await page.locator(this.sfCloseToolbarLink).click();
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
   * @returns {Promise<string>}
   */
  async getHelpDocumentURL(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.helpDocumentURL, 'data');
  }

  /**
   * Get growl message content
   * @param page {Page} Browser tab
   * @param timeout {number} Timeout to wait for the selector
   * @return {Promise<string|null>}
   */
  async getGrowlMessageContent(page: Page, timeout: number = 10000): Promise<string | null> {
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
        await page.locator(this.growlCloseButton).click();
      } catch (e) {
        // If element does not exist it's already not visible
      }

      growlNotVisible = await this.elementNotVisible(page, this.growlMessageBlock, 2000);
    }

    await this.waitForHiddenSelector(page, this.growlMessageBlock);
  }

  /**
   * Return if an alert block is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async hasAlertBlock(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.alertBlock, 1000);
  }

  /**
   * Close alert block
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async closeAlertBlock(page: Page): Promise<void> {
    if (await this.elementVisible(page, this.alertBlockCloseButton, 1000)) {
      await this.waitForSelectorAndClick(page, this.alertBlockCloseButton);
    }
  }

  /**
   * Get error message from alert danger block
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertDangerBlockParagraphContent(page: Page): Promise<string> {
    await this.elementVisible(page, this.alertDangerBlockParagraph, 2000);
    return this.getTextContent(page, this.alertDangerBlockParagraph);
  }

  /**
   * Get alert block content
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertBlockContent(page: Page): Promise<string> {
    await this.elementVisible(page, this.alertTextBlock, 2000);
    return this.getTextContent(page, this.alertTextBlock);
  }

  /**
   * Get text content of alert success block
   * @param page {Frame|Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertSuccessBlockContent(page: Frame | Page): Promise<string> {
    await this.elementVisible(page, this.alertSuccessBlock, 2000);
    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Get text content of alert success block paragraph
   * @param page {Frame|Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertSuccessBlockParagraphContent(page: Frame | Page): Promise<string> {
    await this.elementVisible(page, this.alertSuccessBlockParagraph, 2000);
    return this.getTextContent(page, this.alertSuccessBlockParagraph);
  }

  /**
   * Get text content of alert block paragraph
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertInfoBlockParagraphContent(page: Page): Promise<string> {
    await this.elementVisible(page, this.alertInfoBlockParagraph, 2000);
    return this.getTextContent(page, this.alertInfoBlockParagraph);
  }

  /**
   * Navigate to BO page without token
   * @param page {Page} Browser tab
   * @param url {string} Url to BO page
   * @param continueToPage {boolean} True to continue false to cancel and return to dashboard page
   * @returns {Promise<void>}
   */
  async navigateToPageWithInvalidToken(page: Page, url: string, continueToPage: boolean = true): Promise<void> {
    await this.goTo(page, url);
    if (await this.elementVisible(page, this.invalidTokenContinueLink, 10000)) {
      await this.clickAndWaitForURL(
        page,
        continueToPage ? this.invalidTokenContinueLink : this.invalidTokenCancelLink,
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
    await this.setValue(page, this.navbarSearchInput, query);
    await page.keyboard.press('Enter');
    await page.waitForSelector(this.navbarSearchInput);
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

  // Multistore methods
  /**
   * Click on multistore header
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnMultiStoreHeader(page: Page): Promise<void> {
    await page.locator(this.multistoreButton).click();
  }

  /**
   * Choose shop
   * @param page {Page} Browser tab
   * @param shopNumber
   * @returns {Promise<void>}
   */
  async chooseShop(page: Page, shopNumber: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.chooseShopName(shopNumber));
  }

  /**
   * View my store
   * @param page {Page} Browser tab
   * @returns {Promise<Page>}
   */
  async viewMyStore(page: Page): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.viewMyStoreButton);
  }

  /**
   * Get store color
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getShopColor(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.multistoreTopBar, 'style');
  }

  /**
   * Get store name
   * @param page
   * @returns {Promise<string>}
   */
  async getShopName(page: Page): Promise<string> {
    return this.getTextContent(page, this.storeName);
  }
}

module.exports = BOBasePage;
