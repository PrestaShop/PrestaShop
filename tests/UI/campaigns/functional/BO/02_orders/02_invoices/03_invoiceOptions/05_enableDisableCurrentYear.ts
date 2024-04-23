// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import invoicesPage from '@pages/BO/orders/invoices';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

import {
  // Import data
  dataOrderStatuses,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';
import type {BrowserContext, Page} from 'playwright';

use(chaiString);

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_enableDisableCurrentYear';

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
describe('BO - Orders - Invoices : Enable/Disable current year', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const today: Date = new Date();
  const currentYear: string = today.getFullYear().toString();

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
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableCurrentYear', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.invoicesLink,
      );
      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should enable add current year to invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableCurrentYear', baseContext);

      await invoicesPage.enableAddCurrentYearToInvoice(page, true);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });

    describe('Choose the position of the year at the end and check it', async () => {
      it('should choose the position \'After the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToEnd', baseContext);

        // Choose the option 'After the sequential number' (ID = 0)
        await invoicesPage.chooseInvoiceOptionsYearPosition(page, 0);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage1', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateStatusEnabledCurrentYearInTheEnd', baseContext);

        const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
        expect(result).to.equal(dataOrderStatuses.shipped.name);
      });

      it('should check that the invoice file name contain current year at the end', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledCurrentYearAtTheEndOfFile', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        expect(fileName).to.endWith(currentYear);
      });
    });

    describe('Choose the position of the year at the beginning and check it', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage1', baseContext);

        await orderPageTabListBlock.goToSubMenu(
          page,
          orderPageTabListBlock.ordersParentLink,
          orderPageTabListBlock.invoicesLink,
        );

        const pageTitle = await invoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(invoicesPage.pageTitle);
      });

      it('should choose \'Before the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToBeginning', baseContext);

        // Choose the option 'Before the sequential number' (ID = 1)
        await invoicesPage.chooseInvoiceOptionsYearPosition(page, 1);

        const textMessage = await invoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it('should check that the invoice file name contain current year at the beginning', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrentYearAtTheBeginningOfFile', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        expect(fileName).to.startWith(`IN${currentYear}`);
      });
    });
  });

  describe('Disable add current year to invoice number then check the invoice file name', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableCurrentYear', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should disable add current year to invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCurrentYear', baseContext);

      await invoicesPage.enableAddCurrentYearToInvoice(page, false);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });

    describe('Check the invoice file Name', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

        await invoicesPage.goToSubMenu(
          page,
          invoicesPage.ordersParentLink,
          invoicesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage3', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it('should check that the invoice file name does not contain the current year', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledCurrentYear', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        expect(fileName).to.not.contains(currentYear);
      });
    });
  });
});
