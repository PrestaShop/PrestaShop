require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const DeliverySlipsPage = require('@pages/BO/orders/deliverySlips/index');
const OrdersPage = require('@pages/BO/orders');
const ViewOrderPage = require('@pages/BO/orders/view');
// Importing data
const {Statuses} = require('@data/demo/orders');
const DeliverySlipOptionsFaker = require('@data/faker/deliverySlipOptions');

let browser;
let page;
let fileName;
const deliverySlipData = new DeliverySlipOptionsFaker();
const defaultPrefix = '#DE';

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
Edit delivery slip prefix
Change the Order status to Shipped
Check the delivery slip file name
Back to the default delivery slip prefix value
 */
describe('Edit delivery slip prefix and check the generated file name', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe(`Change the delivery slip prefix to '${deliverySlipData.prefix}'`, async () => {
    it('should go to delivery slip page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.deliverySlipslink,
      );

      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.deliverySlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.deliverySlipsPage.pageTitle);
    });

    it(`should change the delivery slip prefix to ${deliverySlipData.prefix}`, async function () {
      await this.pageObjects.deliverySlipsPage.changePrefix(deliverySlipData.prefix);
      const textMessage = await this.pageObjects.deliverySlipsPage.saveDeliverySlipOptions();
      await expect(textMessage).to.contains(this.pageObjects.deliverySlipsPage.successfulUpdateMessage);
    });
  });

  describe(`Change the order status to '${Statuses.shipped.status}' and check the file Name`, async () => {
    it('should go to the orders page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.ordersLink,
      );

      const pageTitle = await this.pageObjects.ordersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.ordersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await this.pageObjects.ordersPage.goToOrder(1);
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it(`should change the order status to '${Statuses.shipped.status}' and check it`, async function () {
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.be.true;
    });

    it(`should check that the delivery slip file name contain '${deliverySlipData.prefix}'`, async function () {
      fileName = await this.pageObjects.viewOrderPage.getFileName(3);
      expect(fileName).to.contains(deliverySlipData.prefix.replace('#', '').trim());
    });
  });

  describe(`Back to the default delivery slip prefix value '${defaultPrefix}'`, async () => {
    it('should go to delivery slips page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.ordersParentLink,
        this.pageObjects.boBasePage.deliverySlipslink,
      );

      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.deliverySlipsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.deliverySlipsPage.pageTitle);
    });

    it(`should change the delivery slip prefix to '${defaultPrefix}'`, async function () {
      await this.pageObjects.deliverySlipsPage.changePrefix(defaultPrefix);
      const textMessage = await this.pageObjects.deliverySlipsPage.saveDeliverySlipOptions();
      await expect(textMessage).to.contains(this.pageObjects.deliverySlipsPage.successfulUpdateMessage);
    });
  });
});
