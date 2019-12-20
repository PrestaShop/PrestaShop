require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const DeliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const OrdersPage = require('@pages/BO/orders/index');
const ViewOrderPage = require('@pages/BO/orders/view');
// Importing data
const {Statuses} = require('@data/demo/orders');

let browser;
let page;
const today = new Date();
// Create a start and end date that there is no delivery slip
const dateFrom = `${today.getFullYear() + 1}-${today.getMonth() + 1}-${today.getDate() - 1}`;
const dateTo = `${today.getFullYear() + 1}-${today.getMonth() + 1}-${today.getDate()}`;
const fileName = 'deliveries.pdf';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    deliverySlipsPage: new DeliverySlipsPage(page),
    ordersPage: new OrdersPage(page),
    viewOrderPage: new ViewOrderPage(page),
  };
};

/*
Create order
Create delivery slip
Generate delivery slip file by date
 */
describe('Generate Delivery slip file by date', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated delivery slip */
    await files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${fileName}`);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Create delivery slip', async () => {
    it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );
      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the order page', async function () {
      await this.pageObjects.ordersPage.goToOrder(1);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.be.true;
    });

    it('should check the delivery slip document', async function () {
      const documentName = await this.pageObjects.viewOrderPage.getDocumentName(3);
      await expect(documentName).to.be.equal('Delivery slip');
    });
  });

  describe('Generate delivery slip by date', async () => {
    it('should go to delivery slips page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.deliverySlipslink,
      );
      const pageTitle = await this.pageObjects.deliverySlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.deliverySlipsPage.pageTitle);
    });

    it('should generate PDF file by date and check the file existence', async function () {
      await this.pageObjects.deliverySlipsPage.generatePDFByDate();
      const exist = await files.checkFileExistence(fileName);
      await expect(exist).to.be.true;
    });

    it('should check the error message when there is no delivery slip in the entered date', async function () {
      await this.pageObjects.deliverySlipsPage.generatePDFByDate(dateFrom, dateTo);
      const textMessage = await this.pageObjects.deliverySlipsPage.getTextContent(
        this.pageObjects.deliverySlipsPage.alertTextBlock,
      );
      await expect(textMessage).to.equal(this.pageObjects.deliverySlipsPage.errorMessageWhenGenerateFileByDate);
    });
  });
});
