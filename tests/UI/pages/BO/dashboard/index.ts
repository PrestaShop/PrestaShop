import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';

/**
 * Dashboard page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class Dashboard extends BOBasePage {
  public readonly pageTitle: string;

  private readonly demoModeButton: string;

  private readonly demoModeToggle: (toEnable: string) => string;

  private readonly salesScore: string;

  private readonly onlineVisitorLink: string;

  private readonly activeShoppingCartsLink: string;

  private readonly dashboardLiveSection: string;

  private readonly onlineVisitorsNumber: string;

  private readonly activeShoppingCartNumber: string;

  private readonly dashboardPendingSection: string;

  private readonly dashboardOrdersLink: string;

  private readonly ordersNumber: string;

  private readonly returnExchangeLink: string;

  private readonly returnExchangeNumber: string;

  private readonly abandonedCartsLink: string;

  private readonly abandonedCartsNumber: string;

  private readonly outOfStockProductsLink: string;

  private readonly outOfStockProductsNumber: string;

  private readonly dashboardNotificationsSection: string;

  private readonly newMessagesLink: string;

  private readonly newMessagesNumber: string;

  private readonly productReviewsLink: string;

  private readonly productReviewsNumber: string;

  private readonly dashboardCustomersSection: string;

  private readonly newCustomersNumber: string;

  private readonly newCustomersLink: string;

  private readonly newRegistrationsNumber: string;

  private readonly newSubscriptionsLink: string;

  private readonly totalSubscribersNumber: string;

  private readonly totalSubscribersLink: string;

  private readonly dashboardTrafficSections: string;

  private readonly visitsLink: string;

  private readonly visitsNumber: string;

  private readonly uniqueVisitorsNumber: string;

  private readonly uniqueVisitorsLink: string;

  private readonly dashboardTrafficSourceSection: string;

  private readonly configureLink: string;

  private readonly configureForm: string;

  private readonly recentOrdersTitle: string;

  private readonly recentOrdersTable: string;

  private readonly recentOrdersTableRow: (row: number) => string;

  private readonly recentOrdersTableRowDetailsIcon: (row: number) => string;

  private readonly bestSellersTab: string;

  private readonly bestSellersTabTitle: string;

  private readonly bestSellersTable: string;

  private readonly mostViewedTab: string;

  private readonly mostViewedTabTitle: string;

  private readonly mostViewedTable: string;

  private readonly topSearchersTab: string;

  private readonly topSearchersTabTitle: string;

  private readonly topSearchersTable: string;

  private readonly configureProductsAndSalesLink: string;

  private readonly configureProductsAndSalesForm: string;

  private readonly helpCardButton: string;

  private readonly helpCardDocument: string;

  private readonly helpCardDocumentTitle: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on dashboard page
   */
  constructor() {
    super();

    this.pageTitle = `Dashboard • ${global.INSTALL.SHOP_NAME}`;

    // Demo mode selectors
    this.demoModeButton = '#page-header-desc-configuration-switch_demo';
    this.demoModeToggle = (toEnable: string) => `.process-icon-toggle-${toEnable}.switch_demo`;
    this.salesScore = '#sales_score';
    // Selectors of help card
    this.helpCardButton = '#toolbar-nav a.btn-help';
    this.helpCardDocument = '#help-container div.page-wrap';
    this.helpCardDocumentTitle = '#help-container section.article h1';
    // Selectors of Products and sales block
    this.recentOrdersTitle = '#dash_recent_orders div.panel-heading';
    this.recentOrdersTable = '#table_recent_orders';
    this.recentOrdersTableRow = (row: number) => `#table_recent_orders tbody tr:nth-child(${row})`;
    this.recentOrdersTableRowDetailsIcon = (row: number) => `${this.recentOrdersTableRow(row)} #details a[href*='vieworder']`;
    this.bestSellersTab = '#dashproducts a[href*=\'#dash_best_sellers\']';
    this.bestSellersTabTitle = '#dash_best_sellers div.panel-heading';
    this.bestSellersTable = '#table_best_sellers';
    this.mostViewedTab = '#dashproducts a[href*=\'#dash_most_viewed\']';
    this.mostViewedTabTitle = '#dash_most_viewed div.panel-heading';
    this.mostViewedTable = '#table_most_viewed';
    this.topSearchersTab = '#dashproducts a[href*=\'#dash_top_search\']';
    this.topSearchersTabTitle = '#dash_top_search div.panel-heading';
    this.topSearchersTable = '#table_top_10_most_search';
    this.configureProductsAndSalesLink = '#dashproducts i.process-icon-configure';
    this.configureProductsAndSalesForm = '#fieldset_0_1 div.form-wrapper';
    // Activity overview selectors
    this.dashboardLiveSection = '#dash_live span.data_label';
    this.onlineVisitorLink = `${this.dashboardLiveSection} a[href*='controller=AdminStats']`;
    this.onlineVisitorsNumber = '#online_visitor';
    this.activeShoppingCartsLink = `${this.dashboardLiveSection} a[href*='controller=AdminCarts']`;
    this.activeShoppingCartNumber = '#active_shopping_cart';
    this.dashboardPendingSection = '#dash_pending span.data_label';
    this.dashboardOrdersLink = `${this.dashboardPendingSection} a[href*='sell/orders']`;
    this.ordersNumber = '#pending_orders';
    this.returnExchangeLink = `${this.dashboardPendingSection} a[href*='controller=AdminReturn']`;
    this.returnExchangeNumber = '#return_exchanges';
    this.abandonedCartsLink = `${this.dashboardPendingSection} a[href*='controller=AdminCarts']`;
    this.abandonedCartsNumber = '#abandoned_cart';
    this.outOfStockProductsLink = `${this.dashboardPendingSection} a[href*='catalog/monitoring']`;
    this.outOfStockProductsNumber = '#products_out_of_stock';
    this.dashboardNotificationsSection = '#dash_notifications span.data_label';
    this.newMessagesLink = `${this.dashboardNotificationsSection} a[href*='controller=AdminCustomerThreads']`;
    this.newMessagesNumber = '#new_messages';
    this.productReviewsLink = `${this.dashboardNotificationsSection} a[href*='controller=AdminModules']`;
    this.productReviewsNumber = '#product_reviews';
    this.dashboardCustomersSection = '#dash_customers span.data_label';
    this.newCustomersNumber = '#new_customers';
    this.newCustomersLink = `${this.dashboardCustomersSection} a[href*='sell/customers']`;
    this.newRegistrationsNumber = '#new_registrations';
    this.newSubscriptionsLink = `${this.dashboardCustomersSection} a[href*='controller=AdminStats']`;
    this.totalSubscribersNumber = '#total_suscribers';
    this.totalSubscribersLink = `${this.dashboardCustomersSection} a[href*='controller=AdminModules']`;
    this.dashboardTrafficSections = '#dash_traffic';
    this.visitsLink = `${this.dashboardTrafficSections} li:nth-child(1) span.data_label a[href*='controller=AdminStats']`;
    this.visitsNumber = '#visits';
    this.uniqueVisitorsNumber = '#unique_visitors';
    this.uniqueVisitorsLink = `${this.dashboardTrafficSections} li:nth-child(2) span.data_label a[href*='controller=AdminStats']`;
    this.dashboardTrafficSourceSection = '#dash_traffic_source';
    this.configureLink = '#dashactivity span.panel-heading-action i.process-icon-configure';
    this.configureForm = '#fieldset_0 div.form-wrapper';
  }

  /* Methods */

  /**
   * Set demo mode
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable demo mode, false if not
   */
  async setDemoMode(page: Page, toEnable: boolean): Promise<void> {
    const isDemoModeOn = await this.elementVisible(page, this.demoModeToggle('on'), 2000);

    if ((toEnable && !isDemoModeOn) || (!toEnable && isDemoModeOn)) {
      await this.waitForSelectorAndClick(page, this.demoModeButton);
      await page.waitForTimeout(2000);
    }
  }

  /**
   * Get sales score
   * @param page {Page} Browser tab
   */
  async getSalesScore(page: Page): Promise<number> {
    const text = await this.getTextContent(page, this.salesScore);

    if (text === null) {
      return 0;
    }
    const regexMatch: RegExpMatchArray | null = text.match(/\d+(\.\d+)?/g);

    if (regexMatch === null) {
      return 0;
    }
    const salesScore: string = regexMatch.toString().replace(',', '');

    return parseFloat(salesScore);
  }

  /**
   * Get number of online visitors
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOnlineVisitors(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.onlineVisitorsNumber, 1000);
  }

  /**
   * Click on online visitors link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnOnlineVisitorsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.onlineVisitorLink);
  }

  /**
   * Click active shopping carts link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnActiveShoppingCartsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.activeShoppingCartsLink);
  }

  /**
   * Get number of active shopping carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfActiveShoppingCarts(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.activeShoppingCartNumber, 1000);
  }

  /**
   * Click on orders link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnOrdersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.dashboardOrdersLink);
  }

  /**
   * Get number of orders
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfOrders(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.ordersNumber, 1000);
  }

  /**
   * Click on return exchange link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnReturnExchangeLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.returnExchangeLink);
  }

  /**
   * Get number of return exchange
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfReturnExchange(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.returnExchangeNumber, 1000);
  }

  /**
   * Click on abandoned carts link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnAbandonedCartsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.abandonedCartsLink);
  }

  /**
   * Get number of abandoned carts
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfAbandonedCarts(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.abandonedCartsNumber, 1000);
  }

  /**
   * Click on out of stock products link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnOutOfStockProductsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.outOfStockProductsLink);
  }

  /**
   * Get number of out of stock products
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getOutOfStockProducts(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.outOfStockProductsNumber);
  }

  /**
   * Click on new messages link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnNewMessagesLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newMessagesLink);
  }

  /**
   * Get number of new messages
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewMessages(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.newMessagesNumber, 1000);
  }

  /**
   * Get number of reviews
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfProductReviews(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.productReviewsNumber, 1000);
  }

  /**
   * Click on products reviews link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnProductReviewsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.productReviewsLink);
  }

  /**
   * Get the number of new customers
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewCustomers(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.newCustomersNumber, 1000);
  }

  /**
   * Click on new customers link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnNewCustomersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newCustomersLink);
  }

  /**
   * Get number of new subscriptions
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfNewSubscriptions(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.newRegistrationsNumber, 1000);
  }

  /**
   * Click on new subscriptions link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnNewSubscriptionsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.newSubscriptionsLink);
  }

  /**
   * Get number of total subscribers
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfTotalSubscribers(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.totalSubscribersNumber, 1000);
  }

  /**
   * Click on total subscribers link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnTotalSubscribersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.totalSubscribersLink);
  }

  /**
   * Get number of visits
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfVisits(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.visitsNumber, 1000);
  }

  /**
   * Click on visits link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnVisitsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.visitsLink);
  }

  /**
   * Get number of unique visitors
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfUniqueVisitors(page: Page): Promise<number> {
    return this.getNumberFromText(page, this.uniqueVisitorsNumber, 1000);
  }

  /**
   * Click on unique visitors link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickOnUniqueVisitorsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.uniqueVisitorsLink);
  }

  /**
   * Get traffic sources
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getTrafficSources(page: Page): Promise<string> {
    return this.getTextContent(page, this.dashboardTrafficSourceSection);
  }

  /**
   * Click on configure link
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnConfigureLink(page: Page): Promise<boolean> {
    await page.locator(this.configureLink).click();

    return this.elementVisible(page, this.configureForm, 1000);
  }

  /**
   * Get recent orders title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getRecentOrdersTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.recentOrdersTitle);
  }

  /**
   *Click on details button of recent orders table
   * @param page {Page} Browser tab
   * @param row {number} Row on orders table
   * @returns {Promise<void>}
   */
  async clickOnDetailsButtonOfRecentOrdersTable(page: Page, row: number = 1): Promise<void> {
    await this.clickAndWaitForURL(page, this.recentOrdersTableRowDetailsIcon(row));
  }

  /**
   * Go to best sellers tab
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToBestSellersTab(page: Page): Promise<void> {
    await page.locator(this.bestSellersTab).click();
  }

  /**
   * Get best sellers tab title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getBestSellersTabTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.bestSellersTabTitle);
  }

  /**
   * Is best sellers table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isBestSellersTableVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.bestSellersTable, 1000);
  }

  /**
   * Go to most viewed tab
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToMostViewedTab(page: Page): Promise<void> {
    await page.locator(this.mostViewedTab).click();
  }

  /**
   * Get most viewed tab title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getMostViewedTabTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.mostViewedTabTitle);
  }

  /**
   * Is most viewed table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isMostViewedTableVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.mostViewedTable, 1000);
  }

  /**
   * Go to top searchers tab
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToTopSearchersTab(page: Page): Promise<void> {
    await page.locator(this.topSearchersTab).click();
  }

  /**
   * Get top searchers tab title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTopSearchersTabTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.topSearchersTabTitle);
  }

  /**
   * Is top searchers table visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isTopSearchersTableVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.topSearchersTable, 1000);
  }

  /**
   * Click on configure products and sales link
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnConfigureProductsAndSalesLink(page: Page): Promise<boolean> {
    await page.locator(this.configureProductsAndSalesLink).click();

    return this.elementVisible(page, this.configureProductsAndSalesForm, 1000);
  }

  /**
   * Open help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async openHelpCard(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.helpCardButton);

    return this.elementVisible(page, this.helpCardDocument, 2000);
  }

  /**
   * Close help sidebar
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async closeHelpCard(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.helpCardButton);

    return this.elementNotVisible(page, this.helpCardDocument, 2000);
  }

  /**
   * Get help document title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getHelpDocumentTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.helpCardDocumentTitle);
  }
}

export default new Dashboard();
