require('module-alias/register');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const deliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const ordersPage = require('@pages/BO/orders/index');
const orderPageTabListBlock = require('@pages/BO/orders/view/tabListBlock');

// Import data
const {Statuses} = require('@data/demo/orderStatuses');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_deliverSlips_generateDeliverySlipByDate';

// Using chai
const {expect} = require('chai');

let browserContext;
let page;
const futureDate = getDateFormat('yyyy-mm-dd', 'future');

/*
Update the last order status to shipped
Create delivery slip
Generate delivery slip file by date
 */
describe('BO - Orders - Delivery slips : Generate Delivery slip file by date', async () => {
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

  describe('Create delivery slip', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to the last order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);
      const pageTitle = await orderPageTabListBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await orderPageTabListBlock.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should check the delivery slip document name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentName', baseContext);

      const documentType = await orderPageTabListBlock.getDocumentType(page, 3);
      await expect(documentType).to.be.equal('Delivery slip');
    });
  });

  describe('Generate delivery slip by date', async () => {
    it('should go to \'Orders > Delivery slips\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliverySlipsPage', baseContext);

      await orderPageTabListBlock.goToSubMenu(
        page,
        orderPageTabListBlock.ordersParentLink,
        orderPageTabListBlock.deliverySlipslink,
      );

      const pageTitle = await deliverySlipsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(deliverySlipsPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateDeliverySlips', baseContext);

      // Generate delivery slips
      const filePath = await deliverySlipsPage.generatePDFByDateAndDownload(page);

      const exist = await files.doesFileExist(filePath);
      await expect(exist).to.be.true;
    });

    it('should check the error message when there is no delivery slip at the entered date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoDeliverySlipsErrorMessage', baseContext);

      // Generate delivery slips and get error message
      const textMessage = await deliverySlipsPage.generatePDFByDateAndFail(page, futureDate, futureDate);
      await expect(textMessage).to.equal(deliverySlipsPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
