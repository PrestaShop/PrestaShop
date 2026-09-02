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
  FakerOrderInvoice,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_invoicePrefix';

/*
Edit invoice prefix
Change the Order status to Shipped
Check the invoice file name
Back to the default invoice prefix value
Check the invoice file name
 */
describe('BO - Orders - Invoices : Update invoice prefix and check the generated invoice file name', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;

  const invoiceData: FakerOrderInvoice = new FakerOrderInvoice();
  const defaultPrefix: string = '#IN';

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

  describe('Update the invoice prefix', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToUpdatePrefix', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await boInvoicesPage.closeSfToolBar(page);

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it(`should update the invoice prefix to ${invoiceData.prefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateInvoicePrefix', baseContext);

      await boInvoicesPage.changePrefix(page, invoiceData.prefix);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });

  describe('Update the order status and check the invoice file name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForUpdatedPrefix', baseContext);

      await boInvoicesPage.goToSubMenu(
        page,
        boInvoicesPage.ordersParentLink,
        boInvoicesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForUpdatedPrefix', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'UpdateStatusForUpdatedPrefix', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it(`should check that the invoice file name contain the prefix '${invoiceData.prefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstOrderUpdatedPrefix', baseContext);

      // Get invoice file name
      fileName = await boOrdersViewBlockTabListPage.getFileName(page);
      expect(fileName).to.contains(invoiceData.prefix.replace('#', '').trim());
    });
  });

  describe('Back to the default invoice prefix value', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageForDefaultPrefix', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.invoicesLink,
      );

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it(`should change the invoice prefix to '${defaultPrefix}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultPrefix', baseContext);

      await boInvoicesPage.changePrefix(page, defaultPrefix);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });

  describe('Check the default prefix in the invoice file Name', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageForDefaultPrefix', baseContext);

      await boInvoicesPage.goToSubMenu(
        page,
        boInvoicesPage.ordersParentLink,
        boInvoicesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageForDefaultPrefix', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should check that the invoice file name contain the default prefix ${defaultPrefix}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDefaultPrefixInInvoice', baseContext);

      // Get invoice file name
      fileName = await boOrdersViewBlockTabListPage.getFileName(page);
      expect(fileName).to.contains(defaultPrefix.replace('#', '').trim());
    });
  });
});
