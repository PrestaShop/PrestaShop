require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');
const InvoiceOptionsFaker = require('@data/faker/invoice');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_invoicePrefix';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let fileName;
const invoiceData = new InvoiceOptionsFaker();
const defaultPrefix = '#IN';


/*
Edit invoice prefix
Change the Order status to Shipped
Check the invoice file name
Back to the default invoice prefix value
Check the invoice file name
 */
describe('BO - Orders - Invoices : Update invoice prefix and check the generated invoice file name', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Update the invoice prefix', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToUpdatePrefix', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.invoicesLink,
      );

      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it(`should update the invoice prefix to ${invoiceData.prefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateInvoicePrefix', baseContext);

      await invoicesPage.changePrefix(page, invoiceData.prefix);
      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  describe('Update the order status and check the invoice file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'UpdateStatusForUpdatedPrefix', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it(`should check that the invoice file name contain the prefix '${invoiceData.prefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await viewOrderPage.getFileName(page);
      expect(fileName).to.contains(invoiceData.prefix.replace('#', '').trim());
    });
  });

  describe('Back to the default invoice prefix value', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageForDefaultPrefix', baseContext);

      await viewOrderPage.goToSubMenu(
        page,
        viewOrderPage.ordersParentLink,
        viewOrderPage.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it(`should change the invoice prefix to '${defaultPrefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultPrefix', baseContext);

      await invoicesPage.changePrefix(page, defaultPrefix);
      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  describe('Check the default prefix in the invoice file Name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForDefaultPrefix', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForDefaultPrefix', baseContext);

      await ordersPage.goToOrder(page, 1);
      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it(`should check that the invoice file name contain the default prefix ${defaultPrefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultPrefixInInvoice', baseContext);

      // Get invoice file name
      fileName = await viewOrderPage.getFileName(page);
      expect(fileName).to.contains(defaultPrefix.replace('#', '').trim());
    });
  });
});
