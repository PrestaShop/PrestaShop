require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAddressesPage = require('@pages/FO/myAccount/addresses');
const foAddAddressesPage = require('@pages/FO/myAccount/addAddress');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');
const orderPageProductsBlock = require('@pages/BO/orders/view/productsBlock');
const creditSlipsPage = require('@pages/BO/orders/creditSlips/index');
const monitoringPage = require('@pages/BO/catalog/monitoring');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {DefaultCustomer} = require('@data/demo/customer');
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_creditSlips_createFilterCreditSlips';

// Table name from monitoring page
const tableName = 'disabled_product';

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

let numberOfOrder2Create = 1;
let creditSlipToAddForExistingOrder = 1;
let numberOfOrder2Refund = numberOfOrder2Create + creditSlipToAddForExistingOrder;
const todayDate = getDateFormat('yyyy-mm-dd');
const todayDateToCheck = getDateFormat('mm/dd/yyyy');
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

/*
Pre-condition n°1:
- Create 7 orders
Pre-condition n°3:
- Change the status of 11 orders to 'Shipped' and create partial refund
Scenario:
- Create 2 credit slips for the same order
- Filter Credit slips table( by ID, Order ID, Date issued From and To)
- Download the 2 credit slip files and check them
 */

describe('BO - Orders - Credit Slips - Sort & Pagination Credit Slips', async () => {

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

    
  for(var i=0; i<numberOfOrder2Create; i++){
    
    // Pre-condition: Create 7 order in FO and change their status
    createOrderByCustomerTest(orderByCustomerData, baseContext);
      
    describe(`Create order with ${Statuses.shipped.status} status`, async () => {

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

      it('should go to the first order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);
        
        // const result = await orderPageTabListBlock.modifyOrderStatus(page, test.args.status);
        const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

    });
    
  }

  ///////////////////////
  describe(`Change order status from n°${i} to n°${creditSlipToAddForExistingOrder}`, async () => {
    for(var i=0; i<creditSlipToAddForExistingOrder; i++){

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

      it(`should go to the order n°${i+1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);
  
        await ordersPage.goToOrder(page, i);
  
        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);
        
        // const result = await orderPageTabListBlock.modifyOrderStatus(page, test.args.status);
        const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

    };
  
  });

  describe('Add a partial refund and create new Document for credit slip', async () => {
    for(var i=0; i<numberOfOrder2Refund; i++){
    
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

      it(`should go to order n°${i} page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedOrderPage', baseContext);

        await ordersPage.goToOrder(page, i);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });

      it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateCreatedOrderStatus', baseContext);

        const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
        await expect(result).to.equal(Statuses.shipped.status);
      });

      // const tests = [
      //   {args: {productID: 1, quantity: 1, documentRow: 4}},
      //   {args: {productID: 1, quantity: 1, documentRow: 5}},
      // ];

      // tests.forEach((test, index) => {
        it(`should create the partial refund n°${index + 1}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `addPartialRefund${index + 1}`, baseContext);

          await orderPageTabListBlock.clickOnPartialRefund(page);

          const textMessage = await orderPageProductsBlock.addPartialRefundProduct(
            page,
            test.args.productID,
            test.args.quantity,
          );
          await expect(textMessage).to.contains(orderPageProductsBlock.partialRefundValidationMessage);
        });

        it('should check the existence of the Credit slip document', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkCreditSlipDocument${index + 1}`, baseContext);

          // Get document name
          const documentType = await orderPageTabListBlock.getDocumentType(page, test.args.documentRow);
          await expect(documentType).to.be.equal('Credit slip');
        });

      // });

    };

  });

  describe('Filter Credit slips', async () => {

    it('should go to Credit slips page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreditSlipsPage', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.creditSlipsLink,
      );

      await creditSlipsPage.closeSfToolBar(page);

      const pageTitle = await creditSlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(creditSlipsPage.pageTitle);      
    });

    it('should reset all filters and get number of credit slips', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCreditSlips = await creditSlipsPage.resetAndGetNumberOfLines(page);
      console.log(numberOfCreditSlips)
      // await page.waitForTimeout(15000)
      await expect(numberOfCreditSlips).to.be.above(0);
    });

  });

  xdescribe('Pagination next and previous', async () => {

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await monitoringPage.selectPaginationLimit(page, tableName, '50');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });
  
    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await monitoringPage.paginationNext(page, tableName);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await monitoringPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await monitoringPage.selectPaginationLimit(page, tableName, '20');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });

  });

});