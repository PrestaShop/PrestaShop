import testContext from '@utils/testContext';
import {expect} from 'chai';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boOrdersPage,
  boOrdersViewBasePage,
  type BrowserContext,
  dataModules,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  FakerOrder,
  modPsGdprBoMain,
  modPsGdprBoTabDataConfig,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_psgdpr_configuration_personalDataManagerForGuest';

describe('BO - Modules - GDPR: Personal data manager for guest', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  // New order by guest data
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });

  // Pre-condition: Create order by guest
  createOrderByGuestTest(orderData, `${baseContext}_preTest_1`);

  describe('Personal data manager for guest', async () => {
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

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Personal data management"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabPersonalDataManagement', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 2);
      expect(isTabVisible).to.be.equal(true);

      const numCompliantModules = await modPsGdprBoTabDataConfig.getNumberCompliantModules(page);
      expect(numCompliantModules).to.equal(5);
    });

    it('should return if there is a customer named "hello"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomerHello', baseContext);

      await modPsGdprBoTabDataConfig.searchCustomerData(page, 'hello');

      const hasCustomerData = await modPsGdprBoTabDataConfig.hasCustomerData(page);
      expect(hasCustomerData).to.equal(false);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36847
    it(`should return if there is a customer named "${customerData.email}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomerGuest', baseContext);

      await modPsGdprBoTabDataConfig.searchCustomerData(page, customerData.email);

      //const hasCustomerData = await modPsGdprBoTabDataConfig.hasCustomerData(page);
      //expect(hasCustomerData).to.equal(true);

      const numCustomerDataResults = await modPsGdprBoTabDataConfig.getNumberCustomerDataResults(page);
      expect(numCustomerDataResults).to.equal(1);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36848
    it('should click on the card', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickResultCard', baseContext);

      await modPsGdprBoTabDataConfig.clickResultCard(page, 1);

      /**
       * A panel is displayed with 7 blocks :
       * - General information
       * - Addresses
       * - Orders
       * - Carts
       * - Messages
       * - Last connections
       * - Module: Newsletter subscription
       * - Module: Product Comments
       * - Module: Mail alerts
       */
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36848
    it('should click on details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDetails', baseContext);

      this.skip();
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderPage', baseContext);

      // View order
      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBasePage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(result).to.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageAfterOrder', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleAfterOrder', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPageAfterOrder', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Personal data management"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabPersonalDataManagementAfterOrder', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 2);
      expect(isTabVisible).to.be.equal(true);

      const numCompliantModules = await modPsGdprBoTabDataConfig.getNumberCompliantModules(page);
      expect(numCompliantModules).to.equal(5);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36847
    it(`should return if there is a customer named "${customerData.email}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomerGuestBeforeDelete', baseContext);

      await modPsGdprBoTabDataConfig.searchCustomerData(page, customerData.email);

      //const hasCustomerData = await modPsGdprBoTabDataConfig.hasCustomerData(page);
      //expect(hasCustomerData).to.equal(true);

      const numCustomerDataResults = await modPsGdprBoTabDataConfig.getNumberCustomerDataResults(page);
      expect(numCustomerDataResults).to.equal(1);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36848
    it('should click on the card', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickResultCardAfterOrder', baseContext);

      await modPsGdprBoTabDataConfig.clickResultCard(page, 1);

      /**
       * A panel is displayed with 7 blocks :
       * - General information
       * - Addresses
       * - Orders
       * - Carts
       * - Messages
       * - Last connections
       * - Module: Newsletter subscription
       * - Module: Product Comments
       * - Module: Mail alerts
       */
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36848
    it('should click on Download Invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDownloadInvoices', baseContext);

      this.skip();

      // Check the order reference
    });

    it('should click on Remove data and Cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickRemoveDataCancel', baseContext);

      const textResult = await modPsGdprBoTabDataConfig.clickResultRemoveData(page, 1, true);
      expect(textResult).to.equal(null);

      const isModalVisible = await modPsGdprBoTabDataConfig.isModalRemoveDataVisible(page);
      expect(isModalVisible).to.equal(false);
    });

    it('should click on Remove data and Confirm', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickRemoveDataConfirm', baseContext);

      const textResult = await modPsGdprBoTabDataConfig.clickResultRemoveData(page, 1, false);
      expect(textResult).to.equal(modPsGdprBoTabDataConfig.messageCustomerDataDeleted);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36865
    it(`should return if there is a customer named "${customerData.email}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomerGuestAfterDelete', baseContext);

      await modPsGdprBoTabDataConfig.reloadPage(page);
      await modPsGdprBoTabDataConfig.searchCustomerData(page, customerData.email);

      const hasCustomerData = await modPsGdprBoTabDataConfig.hasCustomerData(page);
      expect(hasCustomerData).to.equal(false);

      //const numCustomerDataResults = await modPsGdprBoTabDataConfig.getNumberCustomerDataResults(page);
      //expect(numCustomerDataResults).to.equal(0);
    });
  });
});
