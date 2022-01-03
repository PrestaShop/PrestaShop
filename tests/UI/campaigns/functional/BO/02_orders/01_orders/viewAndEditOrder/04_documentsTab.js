require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const invoicesPage = require('@pages/BO/orders/invoices/index');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_documentsTab';

let browserContext;
let page;
let filePath;
const note = 'Test note for document';

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition :
- Create order by default customer
Scenario :
- Disable/Enable invoices and check result
- Check all types of documents( invoice, delivery slip, credit slip) and download them
- Check add note, enter payment buttons
 */

describe('BO - Orders - View and edit order : Check order documents tab', async () => {
  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Disable invoices
  describe('Disable invoices', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.invoicesLink,
      );

      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should disable invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableInvoices', baseContext);

      await invoicesPage.enableInvoices(page, false);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  // 2 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 3 - Check generate invoice button
  describe('Check generate invoice button', async () => {
    it('should click on \'Documents\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab', baseContext);

      const isTabOpened = await viewOrderPage.goToDocumentsTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check that \'Generate invoice\' button is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGenerateInvoiceButton', baseContext);

      const isVisible = await viewOrderPage.isGenerateInvoiceButtonVisible(page);
      await expect(isVisible).to.be.false;
    });
  });

  // 4 - Enable invoices
  describe('Enable invoices', async () => {
    it('should go to \'Orders > Invoices\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToInvoicesPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.invoicesLink,
      );

      await invoicesPage.closeSfToolBar(page);

      const pageTitle = await invoicesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(invoicesPage.pageTitle);
    });

    it('should enable invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableInvoices', baseContext);

      await invoicesPage.enableInvoices(page, true);

      const textMessage = await invoicesPage.saveInvoiceOptions(page);
      await expect(textMessage).to.contains(invoicesPage.successfulUpdateMessage);
    });
  });

  // 5 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 6 - Check documents tab
  describe('Check documents tab', async () => {
    it('should click on \'DocumentS\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab', baseContext);

      const isTabOpened = await viewOrderPage.goToDocumentsTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should check that \'Generate invoice\' button is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGenerateInvoiceButton', baseContext);

      const isVisible = await viewOrderPage.isGenerateInvoiceButtonVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should check that documents number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber0', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(0);
    });

    it('should check the existence of the message \'There is no available document\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const textMessage = await viewOrderPage.getTextColumnFromDocumentsTable(page, 'alert-available', 1);
      await expect(textMessage).to.be.equal(viewOrderPage.noAvailableDocumentsMessage);
    });

    it('should click on \'Generate invoice\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'create invoice', baseContext);

      const textResult = await viewOrderPage.generateInvoice(page);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that documents number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber1', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(1);
    });

    it('should check if \'Invoice\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceDocument', baseContext);

      const documentType = await viewOrderPage.getDocumentType(page, 1);
      await expect(documentType).to.be.equal('Invoice');
    });

    it('should download the \'Invoice\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoice', baseContext);

      filePath = await viewOrderPage.downloadInvoice(page, 1);
      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should add note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNote', baseContext);

      const textResult = await viewOrderPage.setDocumentNote(page, note, 1);
      await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
    });

    it('should check that the button \'Edit note\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditNoteButton', baseContext);

      const isVisible = await viewOrderPage.isEditDocumentNoteButtonVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should delete note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteNote', baseContext);

      const textResult = await viewOrderPage.setDocumentNote(page, '', 1);
      await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
    });

    it('should check that the button \'Add note\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddNoteButton', baseContext);

      const isVisible = await viewOrderPage.isAddDocumentNoteButtonVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should click on \'Enter payment\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentButton', baseContext);

      const amountValue = await viewOrderPage.clickOnEnterPaymentButton(page);
      await expect(amountValue).to.not.equal('');
    });

    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

      const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(Statuses.paymentAccepted.status);
    });

    it('should check that the button \'Enter payment\' is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEnterPaymentButton', baseContext);

      const isVisible = await viewOrderPage.isEnterPaymentButtonVisible(page);
      await expect(isVisible).to.be.false;
    });

    it(`should change the order status to '${Statuses.shipped.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusShipped', baseContext);

      const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(textResult).to.equal(Statuses.shipped.status);
    });

    it('should check that documents number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber2', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(2);
    });

    it('should check if \'Delivery slip\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeliverySlipDocument', baseContext);

      const documentType = await viewOrderPage.getDocumentType(page, 3);
      await expect(documentType).to.be.equal('Delivery slip');
    });

    it('should download \'Delivery slip\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadDeliverySlip', baseContext);

      filePath = await viewOrderPage.downloadInvoice(page, 3);
      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should create \'Partial refund\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPartialRefund', baseContext);

      await viewOrderPage.clickOnPartialRefund(page);

      const textMessage = await viewOrderPage.addPartialRefundProduct(page, 1, 1);
      await expect(textMessage).to.contains(viewOrderPage.partialRefundValidationMessage);
    });

    it('should check if \'Credit slip\' document is created', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

      // Get document name
      const documentType = await viewOrderPage.getDocumentType(page, 4);
      await expect(documentType).to.be.equal('Credit slip');
    });

    it('should download \'Credit slip\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadCreditSlip', baseContext);

      filePath = await viewOrderPage.downloadInvoice(page, 4);
      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });
  });
});
