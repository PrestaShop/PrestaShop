import testContext from '@utils/testContext';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  FakerOrderInvoice,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_invoices_invoiceOptions_otherOptions';

/*
Pre-condition:
- Create order in FO
Scenario:
- Edit Invoice number, Legal free text and Footer text
- Change the create order status to Shipped
- Check the invoice file name
- Back to the default invoice data value
 */
describe('BO - Orders - Invoices : Update \'Invoice number, Legal free text and Footer text\'', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let fileName: string;
  let filePath: string|null;

  const invoiceData: FakerOrderInvoice = new FakerOrderInvoice({legalFreeText: 'Legal free text'});
  const invoiceDefaultData: FakerOrderInvoice = new FakerOrderInvoice({
    prefix: invoiceData.prefix,
    invoiceNumber: '0',
    legalFreeText: '',
    footerText: '',
  });
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order in FO
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    // Delete the invoice file
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Update the Invoice number, Legal free text and Footer text', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEditOptions', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await boInvoicesPage.closeSfToolBar(page);

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should change the invoice number, the invoice legal free text and the invoice footer text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOptions', baseContext);

      await boInvoicesPage.setInputOptions(page, invoiceData);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });

  describe('Create an invoice and check the updated data', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageUpdatedOptions', baseContext);

      await boInvoicesPage.goToSubMenu(
        page,
        boInvoicesPage.ordersParentLink,
        boInvoicesPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageUpdatedOptions', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatusUpdatedOptions', baseContext);

      const result = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should download the invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceUpdatedOptions', baseContext);

      // Download invoice
      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page);
      expect(filePath).to.not.eq(null);

      if (filePath) {
        const exist = await utilsFile.doesFileExist(filePath);
        expect(exist).to.eq(true);
      }
    });

    it('should check that the invoice file name contain the \'Invoice number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedInvoiceNumber', baseContext);

      // Get file name
      fileName = await boOrdersViewBlockTabListPage.getFileName(page);
      expect(fileName).to.contains(invoiceData.invoiceNumber);
    });

    it('should check that the invoice contain the \'Legal free text\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedLegalFreeText', baseContext);

      // Check the existence of the Legal free text
      const exist = await utilsFile.isTextInPDF(filePath, invoiceData.legalFreeText);
      expect(exist, `PDF does not contains this text : ${invoiceData.legalFreeText}`).to.eq(true);
    });

    it('should check that the invoice contain the \'Footer text\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedFooterText', baseContext);

      // Check the existence of the Footer text
      const exist = await utilsFile.isTextInPDF(filePath, invoiceData.footerText);
      expect(exist, `PDF does not contains this text : ${invoiceData.footerText}`).to.eq(true);
    });
  });

  describe('Back to the default data value', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageForDefaultData', baseContext);

      await boOrdersViewBlockTabListPage.goToSubMenu(
        page,
        boOrdersViewBlockTabListPage.ordersParentLink,
        boOrdersViewBlockTabListPage.invoicesLink,
      );

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should change the Invoice number, legal free text and the footer text to default data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultData', baseContext);

      await boInvoicesPage.setInputOptions(page, invoiceDefaultData);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });
});
