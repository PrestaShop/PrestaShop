import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boDeliverySlipsPage,
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

const baseContext: string = 'functional_BO_orders_deliverySlips_generateDeliverySlipByDate';

/*
Update the last order status to shipped
Create delivery slip
Generate delivery slip file by date
 */
describe('BO - Orders - Delivery slips : Generate Delivery slip file by date', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  describe('Create delivery slip', async () => {
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

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check the delivery slip document name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentName', baseContext);

      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 3);
      expect(documentType).to.be.equal('Delivery slip');
    });
  });

  describe('Generate delivery slip by date', async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPage', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.deliverySlipslink,
      );

      const pageTitle = await boDeliverySlipsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDeliverySlipsPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateDeliverySlips', baseContext);

      // Generate delivery slips
      const filePath = await boDeliverySlipsPage.generatePDFByDateAndDownload(page);

      const exist = await utilsFile.doesFileExist(filePath);
      expect(exist).to.eq(true);
    });

    it('should check the error message when there is no delivery slip at the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDeliverySlipsErrorMessage', baseContext);

      // Generate delivery slips and get error message
      const textMessage = await boDeliverySlipsPage.generatePDFByDateAndFail(page, futureDate, futureDate);
      expect(textMessage).to.equal(boDeliverySlipsPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
