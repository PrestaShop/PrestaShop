import testContext from '@utils/testContext';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';
import {expect} from 'chai';

import {
  boDashboardPage,
  boInvoicesPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockPaymentsPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_documentsTab';

/*
Pre-condition :
- Create order by default customer
Scenario :
- Disable/Enable invoices and check result
- Check all types of documents( invoice, delivery slip, credit slip) and download them
- Check add note, enter payment buttons
 */

describe('BO - Orders - View and edit order : Check order documents tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;

  const note: string = 'Test note for document';
  // New order by customer data
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

  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Disable invoices
  describe('Disable invoices', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await boInvoicesPage.closeSfToolBar(page);

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should disable invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableInvoices', baseContext);

      await boInvoicesPage.enableInvoices(page, false);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });

  // 2 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });
  });

  // 3 - Check generate invoice button
  describe('Check generate invoice button', async () => {
    it('should click on \'Documents\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab1', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToDocumentsTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should check that \'Generate invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGenerateInvoiceButton1', baseContext);

      const isVisible = await boOrdersViewBlockTabListPage.isGenerateInvoiceButtonVisible(page);
      expect(isVisible).to.eq(false);
    });
  });

  // 4 - Enable invoices
  describe('Enable invoices', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.invoicesLink,
      );
      await boInvoicesPage.closeSfToolBar(page);

      const pageTitle = await boInvoicesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boInvoicesPage.pageTitle);
    });

    it('should enable invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableInvoices', baseContext);

      await boInvoicesPage.enableInvoices(page, true);

      const textMessage = await boInvoicesPage.saveInvoiceOptions(page);
      expect(textMessage).to.contains(boInvoicesPage.successfulUpdateMessage);
    });
  });

  // 5 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockTabListPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockTabListPage.pageTitle);
    });
  });

  // 6 - Check documents tab
  describe('Check documents tab', async () => {
    it('should click on \'Documents\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab2', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToDocumentsTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should check that \'Generate invoice\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGenerateInvoiceButton2', baseContext);

      const isVisible = await boOrdersViewBlockTabListPage.isGenerateInvoiceButtonVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check that documents number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber0', baseContext);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(0);
    });

    it('should check the existence of the message \'There is no available document\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const textMessage = await boOrdersViewBlockTabListPage.getTextColumnFromDocumentsTable(page, 'alert-available', 1);
      expect(textMessage).to.be.equal(boOrdersViewBlockTabListPage.noAvailableDocumentsMessage);
    });

    it('should click on \'Generate invoice\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'create invoice', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.generateInvoice(page);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it('should check that documents number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber1', baseContext);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(1);
    });

    it('should check if \'Invoice\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceDocument', baseContext);

      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 1);
      expect(documentType).to.be.equal('Invoice');
    });

    it('should download the \'Invoice\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 1);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should add note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNote', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setDocumentNote(page, note, 1);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.updateSuccessfullMessage);
    });

    it('should check that the button \'Edit note\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditNoteButton', baseContext);

      const isVisible = await boOrdersViewBlockTabListPage.isEditDocumentNoteButtonVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should delete note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNote', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setDocumentNote(page, '', 1);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.updateSuccessfullMessage);
    });

    it('should check that the button \'Add note\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddNoteButton', baseContext);

      const isVisible = await boOrdersViewBlockTabListPage.isAddDocumentNoteButtonVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should click on \'Enter payment\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentButton', baseContext);

      await boOrdersViewBlockTabListPage.clickOnEnterPaymentButton(page);

      const amountValue = await boOrdersViewBlockPaymentsPage.getPaymentAmountInputValue(page);
      expect(amountValue).to.not.equal('');
    });

    it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(textResult).to.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should check that the button \'Enter payment\' is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEnterPaymentButton', baseContext);

      const isVisible = await boOrdersViewBlockTabListPage.isEnterPaymentButtonVisible(page);
      expect(isVisible).to.eq(false);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusShipped', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(textResult).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check that documents number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber2', baseContext);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(2);
    });

    it('should check if \'Delivery slip\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipDocument', baseContext);

      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 3);
      expect(documentType).to.be.equal('Delivery slip');
    });

    it('should download \'Delivery slip\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);

      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 3);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should create \'Partial refund\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPartialRefund', baseContext);

      await boOrdersViewBlockTabListPage.clickOnPartialRefund(page);

      const textMessage = await boOrdersViewBlockProductsPage.addPartialRefundProduct(page, 1, 1);
      expect(textMessage).to.contains(boOrdersViewBlockProductsPage.partialRefundValidationMessage);
    });

    it('should check if \'Credit slip\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

      // Get document name
      const documentType = await boOrdersViewBlockTabListPage.getDocumentType(page, 4);
      expect(documentType).to.be.equal('Credit slip');
    });

    it('should download \'Credit slip\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadCreditSlip', baseContext);

      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 4);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });
  });
});
