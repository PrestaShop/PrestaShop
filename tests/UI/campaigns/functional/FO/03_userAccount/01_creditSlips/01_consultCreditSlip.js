require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');
const {createAddressTest} = require('@commonTests/BO/customers/createDeleteAddress');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view/viewOrderBasePage');
const viewOrderProductsBlockPage = require('@pages/BO/orders/view/productsBlock');
const viewOrderTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const creditSlipsPage = require('@pages/FO/myAccount/creditSlips');

// Import demo data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');

// Import faker data
const CustomerFaker = require('@data/faker/customer');
const AddressFaker = require('@data/faker/address');

const baseContext = 'functional_FO_userAccount_creditSlips_consultCreditSlip';

let browserContext;
let page;
let orderReference;
let creditSlipID;
let dateIssued;

const customerData = new CustomerFaker();
const addressData = new AddressFaker({
  email: customerData.email,
  country: 'France',
});
const orderData = {
  customer: customerData,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
  Pre-condition:
  - Create new account on FO
  - Create new address
  - Create order
  Scenario:

  Post condition:

 */
describe('FO - Consult credit slip list & View PDF Credit slip & View order', async () => {
  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);
  // Pre-condition: Create new address
  createAddressTest(addressData, `${baseContext}_preTest_2`);
  // Pre-condition: Create order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Consult Credit slip list in FO', async () => {
    describe('Check there are no credit slips in FO', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

        await homePage.goTo(page, global.FO.URL);

        const result = await homePage.isHomePage(page);
        await expect(result).to.be.true;
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

        await homePage.goToLoginPage(page);
        const pageTitle = await loginPage.getPageTitle(page);
        await expect(pageTitle).to.equal(loginPage.pageTitle);
      });

      it('should login', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginFO', baseContext);

        await loginPage.customerLogin(page, customerData);

        const isCustomerConnected = await loginPage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

        await myAccountPage.goToCreditSlipsPage(page);

        const pageTitle = await creditSlipsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(creditSlipsPage.pageTitle);
      });

      it('should check there no credit slips', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNoCreditSlips', baseContext);

        const alertInfoMessage = await creditSlipsPage.getAlertInfoMessage(page);
        await expect(alertInfoMessage).to.equal(creditSlipsPage.noCreditSlipsInfoMessage);
      });
    });

    describe('Create a partial refund from the BO', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the Orders table by the default customer and check the result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrder', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        await expect(textColumn).to.contains(customerData.lastName);
      });

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

        // View order
        await ordersPage.goToOrder(page, 1);

        const pageTitle = await viewOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
      });

      it(`should change the order status to '${Statuses.paymentAccepted.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const result = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
        await expect(result).to.equal(Statuses.paymentAccepted.status);
      });

      it('should check if the button \'Partial Refund\' is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPartialRefundButton', baseContext);

        const result = await viewOrderPage.isPartialRefundButtonVisible(page);
        await expect(result).to.be.true;
      });

      it('should create \'Partial refund\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createPartialRefund', baseContext);

        await viewOrderPage.clickOnPartialRefund(page);

        const textMessage = await viewOrderProductsBlockPage.addPartialRefundProduct(page, 1, 1);
        await expect(textMessage).to.contains(viewOrderProductsBlockPage.partialRefundValidationMessage);
      });

      it('should check if \'Credit slip\' document is created', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipDocument', baseContext);

        // Get document name
        const documentType = await viewOrderTabListBlock.getDocumentType(page, 3);
        await expect(documentType).to.be.equal('Credit slip');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

        // Get document name
        orderReference = await viewOrderPage.getOrderReference(page);
        await expect(orderReference).is.not.equal('');
      });

      it('should get the identifier and the date issued of the credit slip', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getIdentifierDateIssued', baseContext);

        // Get Credit Slip ID
        creditSlipID = await viewOrderTabListBlock.getFileName(page, 3);
        await expect(creditSlipID).is.not.equal('');

        // Get Date Issued
        dateIssued = await viewOrderTabListBlock.getDocumentDate(page, 3);
        await expect(dateIssued).is.not.equal('');
      });
    });

    describe('Check there are credit slips in FO', async () => {
      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop_1', baseContext);

        // View my shop and init pages
        page = await viewOrderPage.viewMyShop(page);

        await homePage.changeLanguage(page, 'en');
        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to my account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.equal(myAccountPage.pageTitle);
      });

      it('should go credit slips page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

        await myAccountPage.goToCreditSlipsPage(page);

        const pageTitle = await creditSlipsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(creditSlipsPage.pageTitle);
      });

      it('should check the number of credit slips', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberCreditSlips', baseContext);

        const numberCreditSlips = await creditSlipsPage.getNumberOfCreditSlips(page);
        await expect(numberCreditSlips).to.equal(1);
      });

      it('should check that the \'Order reference, Credit Slip ID, Date Issued\' are correct', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreditSlipInfo', baseContext);

        const creditSlipOrderReference = await creditSlipsPage.getOrderReference(page, 1);
        await expect(creditSlipOrderReference).to.equal(orderReference);

        const creditSlipOrderIdentifier = await creditSlipsPage.getCreditSlipID(page, 1);
        await expect(parseInt(creditSlipOrderIdentifier.replace('#', ''), 10)).to.equal(parseInt(creditSlipID, 10));

        const creditSlipDateIssued = await creditSlipsPage.getDateIssued(page, 1);
        await expect(creditSlipDateIssued).to.equal(dateIssued);
      });
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
