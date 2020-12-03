require('module-alias/register');
// Using chai
const {expect} = require('chai');
const chai = require('chai');
chai.use(require('chai-string'));

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders/index');
const viewOrderPage = require('@pages/BO/orders/view');

// Importing data
const {Statuses} = require('@data/demo/orderStatuses');

// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_invoices_invoiceOptions_enableDisableCurrentYear';


let browserContext;
let page;
let fileName;

const today = new Date();
const currentYear = today.getFullYear().toString();

/*
Enable Add current year to invoice number
Choose the option After the sequential number
Change the first Order status to shipped
Check the current year in the invoice file name
Choose the option Before the sequential number
Check the current year in the invoice file name
Disable Add current year to invoice number
Check that the current year does not exist in the invoice file name
 */
describe('Edit invoice prefix and check the generated invoice file name', async () => {
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

  describe('Enable add current year to invoice number then check the invoice file name', async () => {
    describe('Enable add current year to invoice number', async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableCurrentYear', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.invoicesLink,
        );

        await invoicesPage.closeSfToolBar(page);

        const pageTitle = await invoicesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should enable add current year to invoice number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableCurrentYear', baseContext);

        await invoicesPage.enableAddCurrentYearToInvoice(page, true);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Choose the position of the year date at the end', async () => {
      it('should choose \'After the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToEnd', baseContext);

        // Choose the option 'After the sequential number' (ID = 0)
        await invoicesPage.chooseInvoiceOptionsYearPosition(page, 0);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToOrdersPageEnabledCurrentYearInTheEnd',
          baseContext,
        );

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToFirstOrderPageEnabledCurrentYearInTheEnd',
          baseContext,
        );

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateStatusEnabledCurrentYearInTheEnd', baseContext);

        const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

      it('should check that the invoice file name contain current year at the end', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledCurrentYearAtTheEndOfFile', baseContext);

        fileName = await viewOrderPage.getFileName(page);
        expect(fileName).to.endWith(currentYear);
      });
    });

    describe('Choose the position of the year at the beginning', async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToInvoicesPageToChangeCurrentYearPositionToBeginning',
          baseContext,
        );

        await viewOrderPage.goToSubMenu(
          page,
          viewOrderPage.ordersParentLink,
          viewOrderPage.invoicesLink,
        );

        const pageTitle = await invoicesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should choose \'Before the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToBeginning', baseContext);

        // Choose the option 'Before the sequential number' (ID = 1)
        await invoicesPage.chooseInvoiceOptionsYearPosition(page, 1);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToOrdersPageEnabledCurrentYearInTheBeginning',
          baseContext,
        );

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToFirstOrderPageEnabledCurrentYearInTheBeginning',
          baseContext,
        );

        await ordersPage.goToOrder(page, 1);
        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'updateStatusEnabledCurrentYearInTheBeginning',
          baseContext,
        );

        const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

      it('should check that the invoice file name contain current year at the beginning', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'checkEnabledCurrentYearAtTheBeginningOfFile',
          baseContext,
        );

        fileName = await viewOrderPage.getFileName(page);
        expect(fileName).to.startWith(`IN${currentYear}`);
      });
    });
  });

  describe('Disable add current year to invoice number then check the invoice file name', async () => {
    describe('Disable add current year to invoice number', async () => {
      it('should go to invoices page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableCurrentYear', baseContext);

        await viewOrderPage.goToSubMenu(
          page,
          viewOrderPage.ordersParentLink,
          viewOrderPage.invoicesLink,
        );

        const pageTitle = await invoicesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should disable add current year to invoice number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableCurrentYear', baseContext);

        await invoicesPage.enableAddCurrentYearToInvoice(page, false);
        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });
    });

    describe('Check the invoice file Name', async () => {
      it('should go to the orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageDisabledCurrentYear', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageDisabledCurrentYear', baseContext);

        await ordersPage.goToOrder(page, 1);
        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it('should check that the invoice file name does not contain the current year', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledCurrentYear', baseContext);

        fileName = await viewOrderPage.getFileName(page);
        expect(fileName).to.not.contains(currentYear);
      });
    });
  });
});
