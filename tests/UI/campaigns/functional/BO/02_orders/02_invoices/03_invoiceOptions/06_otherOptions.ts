// Import utils
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

// Import BO pages
import ordersPage from '@pages/BO/orders';
import invoicesPage from '@pages/BO/orders/invoices';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

import {
  boDashboardPage,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  FakerOrderInvoice,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    await loginCommon.loginBO(this, page);
  });

  describe('Update the Invoice number, Legal free text and Footer text', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPageToEditOptions', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should change the invoice number, the invoice legal free text and the invoice footer text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOptions', baseContext);

      await invoicesPage.setInputOptions(page, invoiceData);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  describe('Create an invoice and check the updated data', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPageUpdatedOptions', baseContext);

      await invoicesPage.goToSubMenu(
        page,
        invoicesPage.ordersParentLink,
        invoicesPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPageUpdatedOptions', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateStatusUpdatedOptions', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should download the invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceUpdatedOptions', baseContext);

      // Download invoice
      filePath = await orderPageTabListBlock.downloadInvoice(page);
      expect(filePath).to.not.eq(null);

      if (filePath) {
        const exist = await utilsFile.doesFileExist(filePath);
        expect(exist).to.eq(true);
      }
    });

    it('should check that the invoice file name contain the \'Invoice number\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedInvoiceNumber', baseContext);

      // Get file name
      fileName = await orderPageTabListBlock.getFileName(page);
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

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.invoicesLink,
      );

      const pageTitle = await invoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should change the Invoice number, legal free text and the footer text to default data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'backToDefaultData', baseContext);

      await invoicesPage.setInputOptions(page, invoiceDefaultData);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });
});
