import testContext from '@utils/testContext';
import {use, expect} from 'chai';
import chaiString from 'chai-string';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataOrderStatuses,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Enable add current year to invoice number then check the invoice file name', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEnableCurrentYear', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await boInvoicesPage.closeSfToolBar(page);

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should enable add current year to invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableCurrentYear', baseContext);

      await boInvoicesPage.enableAddCurrentYearToInvoice(page, true);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });

    describe('Choose the position of the year at the end and check it', async () => {
      it('should choose the position \'After the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToEnd', baseContext);

        // Choose the option 'After the sequential number' (ID = 0)
        await boInvoicesPage.chooseInvoiceOptionsYearPosition(page, 0);

        const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

        await boInvoicesPage.goToSubMenu(
          page,
          boInvoicesPage.ordersParentLink,
          boInvoicesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage1', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateStatusEnabledCurrentYearInTheEnd', baseContext);

        const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
        expect(result).to.equal(dataOrderStatuses.shipped.name);
      });

      it('should check that the invoice file name contain current year at the end', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledCurrentYearAtTheEndOfFile', baseContext);

        fileName = await boOrdersViewBlockTabListPage.getFileName(page);
        expect(fileName).to.endWith(currentYear);
      });
    });

    describe('Choose the position of the year at the beginning and check it', async () => {
      it('should go to \'Orders > Invoices\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage1', baseContext);

        await boOrdersViewBlockTabListPage.goToSubMenu(
          page,
          boOrdersViewBlockTabListPage.ordersParentLink,
          boOrdersViewBlockTabListPage.invoicesLink,
        );

        const pageTitle = await boInvoicesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
      });

      it('should choose \'Before the sequential number\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeCurrentYearPositionToBeginning', baseContext);

        // Choose the option 'Before the sequential number' (ID = 1)
        await boInvoicesPage.chooseInvoiceOptionsYearPosition(page, 1);

        const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
        expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

        await boInvoicesPage.goToSubMenu(
          page,
          boInvoicesPage.ordersParentLink,
          boInvoicesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it('should check that the invoice file name contain current year at the beginning', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrentYearAtTheBeginningOfFile', baseContext);

        fileName = await boOrdersViewBlockTabListPage.getFileName(page);
        expect(fileName).to.startWith(`IN${currentYear}`);
      });
    });
  });

  describe('Disable add current year to invoice number then check the invoice file name', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToDisableCurrentYear', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.invoicesLink,
      );

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should disable add current year to invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCurrentYear', baseContext);

      await boInvoicesPage.enableAddCurrentYearToInvoice(page, false);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });

    describe('Check the invoice file Name', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

        await boInvoicesPage.goToSubMenu(
          page,
          boInvoicesPage.ordersParentLink,
          boInvoicesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage3', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
      });

      it('should check that the invoice file name does not contain the current year', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledCurrentYear', baseContext);

        fileName = await boOrdersViewBlockTabListPage.getFileName(page);
        expect(fileName).to.not.contains(currentYear);
      });
    });
  });
});
