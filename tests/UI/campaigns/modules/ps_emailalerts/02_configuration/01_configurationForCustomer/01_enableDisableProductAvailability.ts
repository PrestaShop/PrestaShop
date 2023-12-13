// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psEmailAlerts from '@pages/BO/modules/psEmailAlerts';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import foProductPage from '@pages/FO/product';
import {searchResultsPage} from '@pages/FO/searchResults';

// Import data
import Customers from '@data/demo/customers';
import Modules from '@data/demo/modules';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import {faker} from '@faker-js/faker';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_emailalerts_configuration_configurationForCustomer_enableDisableProductAvailability';

describe('Mail alerts module - Customer notifications - Enable/Disable product availability', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const emailValid: string = faker.internet.email();
  const emailInvalid: string = 'test@test';
  const productData: ProductData = new ProductData({
    quantity: 0,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
  });

  // Pre-condition : Create product out of stock not allowed
  createProductTest(productData, `${baseContext}_preTest`);

  describe('BO - Check Configure page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      // Check "Product availability" is set to "Yes
      const isChecked = await psEmailAlerts.getProductAvailabilityStatus(page);
      expect(isChecked).to.eq(true);
    });
  });

  describe('Case 1: You are not logged to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineNoProductAvailability', baseContext);

      await psEmailAlerts.setProductAvailabilityStatus(page, false);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should go to the FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      page = await psEmailAlerts.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should search the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, productData.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const availabilityLabel = await foProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case1DefineYesProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, true);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should reload the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadProductPage', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should fill the block "Email Alerts" with a valid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockValidEmail', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, emailValid);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should fill the block "Email Alerts" with an invalid email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidEmail', baseContext);

      await page.reload();

      const textMessage = await foProductPage.notifyEmailAlert(page, emailInvalid);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with numbers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockNumbers', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, '123456');
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });

    it('should fill the block "Email Alerts" with invalid characters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillBlockInvalidChars', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page, '**¨¨@');
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationEmailInvalid);
    });
  });

  describe('Case 2: You are logged in to your account on FO', async () => {
    it('should define to "No" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineNoProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, false);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await foProductPage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should login on the Front Office', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginFrontOffice', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foProductPage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);
    });

    it('should check the block "Notify when it\' available" is not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockNotPresent', baseContext);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(false);
    });

    it('should define to "Yes" the "Product Availability"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'case2DefineYesProductAvailability', baseContext);

      page = await foProductPage.changePage(browserContext, 0);

      const pageTitle = await psEmailAlerts.getPageSubtitle(page);
      expect(pageTitle).to.eq(psEmailAlerts.pageTitle);

      await psEmailAlerts.setProductAvailabilityStatus(page, true);

      const textMessage = await psEmailAlerts.saveFormCustomerNotifications(page);
      expect(textMessage).to.contains(psEmailAlerts.successfulUpdateMessage);
    });

    it('should check the block "Notify when it\' available"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBlockPresent', baseContext);

      page = await foProductPage.changePage(browserContext, 1);
      await page.reload();

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(productData.name);

      const hasBlockMailAlert = await foProductPage.hasBlockMailAlert(page);
      expect(hasBlockMailAlert).to.be.equal(true);
    });

    it('should click on  "Notify me when available" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickNotifyButton', baseContext);

      const textMessage = await foProductPage.notifyEmailAlert(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationSaved);
    });

    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'alertAlreadyRegistered', baseContext);

      await page.reload();

      const textMessage = await foProductPage.getBlockMailAlertNotification(page);
      expect(textMessage).to.be.equal(foProductPage.messageAlertNotificationAlreadyRegistered);
    });
  });

  deleteProductTest(productData, `${baseContext}_postTest_0`);
});
