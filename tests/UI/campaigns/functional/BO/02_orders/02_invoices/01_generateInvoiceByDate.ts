import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataOrderStatuses,
  type Page,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_invoices_generateInvoiceByDate';

// Generate PDF file by date
describe('BO - Orders - Invoices : Generate PDF file by date', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  const todayDate: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const futureDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'future');

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

  describe('Create an invoice by changing the first order status', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters and get number of orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });
  });

  describe('Generate invoice by date', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.invoicesLink,
      );

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGeneratedInvoicesPdfFile', baseContext);

      // Generate PDF
      filePath = await boInvoicesPage.generatePDFByDateAndDownload(page, todayDate, todayDate);

      const exist = await utilsFile.doesFileExist(filePath);
      expect(exist, 'File does not exist').to.eq(true);
    });

    it('should check the error message when there is no invoice in the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessageNonexistentInvoice', baseContext);

      // Generate PDF
      const textMessage = await boInvoicesPage.generatePDFByDateAndFail(page, futureDate, futureDate);

      expect(textMessage).to.equal(boInvoicesPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
