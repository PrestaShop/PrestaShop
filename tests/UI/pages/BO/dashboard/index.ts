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
    // Activity overview selectors
    this.onlineVisitorLink = '#dash_live span.data_label a[href*=\'controller=AdminStats\']';
    this.activeShoppingCartsLink = '#dash_live span.data_label a[href*=\'controller=AdminCarts\']';
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

  async getNumberOfOnlineVisitors(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#online_visitor');
  }

  async clickOnOnlineVisitorsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.onlineVisitorLink);
  }

  async clickOnActiveShoppingCartsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.activeShoppingCartsLink);
  }

  async getActiveShoppingCarts(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#active_shopping_cart');
  }

  async clickOnOrdersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_pending span.data_label a[href*=\'sell/orders\']');
  }

  async getOrdersNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#pending_orders');
  }

  async clickOnReturnExchangeLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_pending span.data_label a[href*=\'controller=AdminReturn\']');
  }

  async getReturnExchangeNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#return_exchanges');
  }

  async clickOnAbandonedCartsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_pending span.data_label a[href*=\'controller=AdminCarts\']');
  }

  async getAbandonedCartsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#abandoned_cart');
  }

  async getOutOfStockProducts(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#products_out_of_stock');
  }

  async clickOnOutOfStockProductsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_pending span.data_label a[href*=\'catalog/monitoring\']');
  }

  async getNewMessagesNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#new_messages');
  }

  async clickOnNewMessagesLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_notifications span.data_label a[href*=\'controller=AdminCustomerThreads\']');
  }

  async getProductReviewsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#product_reviews');
  }

  async clickOnProductReviewsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_notifications span.data_label a[href*=\'controller=AdminModules\']');
  }

  async getNewCustomersNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#new_customers');
  }

  async clickOnNewCustomersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_customers span.data_label a[href*=\'sell/customers\']');
  }

  async getNewSubscriptionsNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#new_registrations');
  }

  async clickOnNewSubscriptionsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_customers span.data_label a[href*=\'controller=AdminStats\']');
  }

  async getTotalSubscribersNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#total_suscribers');
  }

  async clickOnTotalSubscribersLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_customers span.data_label a[href*=\'controller=AdminModules\']');
  }

  async getNumberOfVisits(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#visits');
  }

  async clickOnVisitsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_traffic li:nth-child(1) span.data_label a[href*=\'controller=AdminStats\']');
  }

  async getNumberOfUniqueVisitors(page: Page): Promise<number> {
    return this.getNumberFromText(page, '#unique_visitors');
  }

  async clickOnUniqueVisitorsLink(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, '#dash_traffic li:nth-child(2) span.data_label a[href*=\'controller=AdminStats\']');
  }

  async getTrafficSources(page:Page):Promise<string>{
    return this.getTextContent(page, '#dash_traffic_source');
  }
}

export default new Dashboard();
