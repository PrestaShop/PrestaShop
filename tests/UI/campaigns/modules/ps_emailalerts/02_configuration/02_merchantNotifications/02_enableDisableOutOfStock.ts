// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {setupSmtpConfigTest, resetSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boStockPage,
  type BrowserContext,
  dataModules,
  FakerProduct,
  type MailDev,
  type MailDevEmail,
  modPsEmailAlertsBoMain,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const firstProduct: FakerProduct = new FakerProduct({
    type: 'standard',
    taxRule: 'No tax',
    quantity: 1,
  });

  // Data to create second product out of stock not allowed
  const secondProduct: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

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
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);
  });

  describe(`BO: case 1 - Enable 'Out of stock' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
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

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should enable out of stock and set email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableOutOfStock', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setOutOfStock(page, true, 'demo@prestashop.com');
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('Update product quantity and check email', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.stocksLink,
      );
      await boStockPage.closeSfToolBar(page);

      const pageTitle = await boStockPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStockPage.pageTitle);
    });

    it(`should filter by name '${firstProduct.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks', baseContext);

      await boStockPage.simpleFilter(page, firstProduct.name);

      const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to -3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await boStockPage.updateRowQuantityWithInput(page, 1, -3);
      expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
    });

    it('should check received email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(newMail.subject).to.equal(`[${global.INSTALL.SHOP_NAME}] Product out of stock`);
    });
  });

  describe(`BO: case 2 - Disable 'Out of stock' in the module '${dataModules.psEmailAlerts.name}'`, async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psEmailAlerts.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule2', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psEmailAlerts);
      expect(isModuleVisible).to.equal(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psEmailAlerts.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage2', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psEmailAlerts.tag);

      const pageTitle = await modPsEmailAlertsBoMain.getPageSubtitle(page);
      expect(pageTitle).to.equal(modPsEmailAlertsBoMain.pageTitle);
    });

    it('should disable out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableOutOfStock2', baseContext);

      const successMessage = await modPsEmailAlertsBoMain.setOutOfStock(page, false);
      expect(successMessage).to.contains(modPsEmailAlertsBoMain.successfulUpdateMessage);
    });
  });

  describe('Update product quantity and check email', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.stocksLink,
      );
      await boStockPage.closeSfToolBar(page);

      const pageTitle = await boStockPage.getPageTitle(page);
      expect(pageTitle).to.contains(boStockPage.pageTitle);
    });

    it(`should filter by name '${secondProduct.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStocks2', baseContext);

      await boStockPage.simpleFilter(page, secondProduct.name);

      const numberOfProductsAfterFilter = await boStockPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.equal(1);
    });

    it('should update product quantity to -3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity2', baseContext);

      // Update Quantity and check successful message
      const updateMessage = await boStockPage.updateRowQuantityWithInput(page, 1, -3);
      expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
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
