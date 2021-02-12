require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class Dashboard extends BOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Dashboard • ';

    // Selectors
    this.switchDemoModeButton = toggle => `#page-header-desc-configuration-switch_demo i.process-icon-toggle-${toggle}`;

    // Activity overview selectors
    this.onlineVisitorsNumberSpan = '#online_visitor';
    this.activeShoppingCartsNumberSpan = '#active_shopping_cart';
    this.pendingOrdersNumberSpan = '#pending_orders';
    this.returnExchangesNumberSpan = '#return_exchanges';
    this.abandonedCartsNumberSpan = '#abandoned_cart';
    this.outOfStockProductsNumberSpan = '#products_out_of_stock';
    this.newMessagesNumberSpan = '#new_messages';
    this.productReviewsNumberSpan = '#product_reviews';
    this.newCustomersNumberSpan = '#new_customers';
    this.newSubscribersNumberSpan = '#new_registrations';
    this.totalSubscribersNumberSpan = '#total_suscribers';
    this.visitsNumberSpan = '#visits';
    this.uniqueVisitorsNumberSpan = '#unique_visitors';
    this.trafficSourcesBlock = '#dash_traffic_source';
    this.trafficSourcesPrestashopComNumberSpan = `${this.trafficSourcesBlock} .data_value.size_s`;
  }

  /*
  Methods
   */

  /**
   * Enable/Disable demo mode
   * @param page
   * @param toEnable
   * @returns {Promise<void>}
   */
  async setDemoMode(page, toEnable = true) {
    await this.waitForSelectorAndClick(page, this.switchDemoModeButton(toEnable ? 'off' : 'on'));
  }

  /**
   * Get traffic number
   * @param page
   * @param activity
   * @returns {Promise<number>}
   */
  async getTrafficNumber(page, activity) {
    let selector;
    switch (activity) {
      case 'online_visitor':
        selector = this.onlineVisitorsNumberSpan;
        break;

      case 'active_shopping_cart':
        selector = this.activeShoppingCartsNumberSpan;
        break;

      case 'pending_orders':
        selector = this.pendingOrdersNumberSpan;
        break;

      case 'return_exchanges':
        selector = this.returnExchangesNumberSpan;
        break;

      case 'abandoned_cart':
        selector = this.abandonedCartsNumberSpan;
        break;

      case 'products_out_of_stock':
        selector = this.outOfStockProductsNumberSpan;
        break;

      case 'new_messages':
        selector = this.newMessagesNumberSpan;
        break;

      case 'product_reviews':
        selector = this.productReviewsNumberSpan;
        break;

      case 'new_customers':
        selector = this.newCustomersNumberSpan;
        break;

      case 'new_registrations':
        selector = this.newSubscribersNumberSpan;
        break;

      case 'total_suscribers':
        selector = this.totalSubscribersNumberSpan;
        break;

      case 'visits':
        selector = this.visitsNumberSpan;
        break;

      case 'unique_visitors':
        selector = this.uniqueVisitorsNumberSpan;
        break;

      case 'dash_traffic_source':
        selector = this.trafficSourcesPrestashopComNumberSpan;
        break;

      default:
        throw new Error(`${activity} was not found`);
    }

    return this.getNumberFromText(page, selector);
  }

  /**
   * Get all values
   * @param page
   * @param activity
   * @returns {Promise<[]>}
   */
  async getAllTrafficValues(page, activity) {
    await page.waitForTimeout(2000);
    const allRowsContentTable = [];

    for (let i = 0; i <= 13; i++) {
      const rowContent = await this.getTrafficNumber(page, activity[i]);
      await allRowsContentTable.push(rowContent);
    }

    return allRowsContentTable;
  }
}

module.exports = new Dashboard();
