// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import mailHelper from '@utils/mailHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import emailAlertsPage from '@pages/BO/modules/psEmailAlerts';
import stocksPage from '@pages/BO/catalog/stocks';
import {moduleManager} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import MailDevEmail from '@data/types/maildevEmail';
import MailDev from 'maildev';

const baseContext: string = 'modules_ps_emailalerts_configuration_merchantNotifications_enableDisableOutOfStock';

/*
Pre-condition:
- Setup SMTP parameters
- Create 2 productS
Scenario
- Enable out of stock in email alerts module
- Update first created product quantity to -3
- check email
- Disable out of stock in email alerts module
- Update second created product quantity to -3
- check that mail is not received
Post-condition:
- Reset SMTP parameters
- Delete created products
 */
describe('Mail alerts module - Enable/Disable out of stock', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;

  // Data to create product out of stock not allowed
  const firstProduct: ProductData = new ProductData({
    type: 'standard',
    taxRule: 'No tax',
    quantity: 1,
  });

  // Data to create second product out of stock not allowed
  const secondProduct: ProductData = new ProductData({
    type: 'standard',
    taxRule: 'No tax',
    quantity: 1,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition : Create product
  createProductTest(firstProduct, `${baseContext}_preTest_2`);

  // Pre-condition : Create second product
  createProductTest(secondProduct, `${baseContext}_preTest_3`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Start listening to maildev server
    mailListener = mailHelper.createMailListener();
    mailHelper.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });

    // get all emails
    // @ts-ignore
    mailListener.getAllEmail((err: Error, emails: MailDevEmail[]) => {
      allEmails = emails;
    });
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    mailHelper.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'Out of stock' in the module '${Modules.psEmailAlerts.name}'`, async () => {
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
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });

    it('should enable out of stock and set email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableOutOfStock', baseContext);

      const successMessage = await emailAlertsPage.setOutOfStock(page, true, 'demo@prestashop.com');
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('Update product quantity and check email', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );
      await stocksPage.closeSfToolBar(page);

      const pageTitle = await stocksPage.getPageTitle(page);
      expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it(`should filter by name '${firstProduct.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks', baseContext);

      await stocksPage.simpleFilter(page, firstProduct.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to -3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 1, -3);
      expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(newMail.subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product out of stock`);
    });
  });

  describe(`BO: case 2 - Disable 'Out of stock' in the module '${Modules.psEmailAlerts.name}'`, async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it(`should search the module ${Modules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await moduleManager.searchModule(page, Modules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${Modules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await moduleManager.goToConfigurationPage(page, Modules.psEmailAlerts.tag);

      const pageTitle = await emailAlertsPage.getPageSubtitle(page);
      expect(pageTitle).to.equal(emailAlertsPage.pageTitle);
    });

    it('should disable out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableOutOfStock2', baseContext);

      const successMessage = await emailAlertsPage.setOutOfStock(page, false);
      expect(successMessage).to.contains(emailAlertsPage.successfulUpdateMessage);
    });
  });

  describe('Update product quantity and check email', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );
      await stocksPage.closeSfToolBar(page);

      const pageTitle = await stocksPage.getPageTitle(page);
      expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it(`should filter by name '${secondProduct.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks2', baseContext);

      await stocksPage.simpleFilter(page, secondProduct.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to -3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity2', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 1, -3);
      expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
    });

    it('should check that no email received', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail2', baseContext);

      expect(numberOfEmails).to.equal(allEmails.length);
      expect(newMail.subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product out of stock`);
    });
  });

  // Post-condition : Delete the created product
  deleteProductTest(firstProduct, `${baseContext}_postTest_1`);

  // Post-condition : Delete the created product
  deleteProductTest(secondProduct, `${baseContext}_postTest_2`);

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest_3`);
});
