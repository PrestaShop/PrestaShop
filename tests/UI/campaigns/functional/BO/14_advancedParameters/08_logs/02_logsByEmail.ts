import testContext from '@utils/testContext';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';
import {createProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

import {
  boDashboardPage,
  boLoginPage,
  boLogsPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  dataEmployees,
  FakerProduct,
  type MailDev,
  type MailDevEmail,
  type Page,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_logs_logsByEmail';

describe('BO - Advanced Parameters - Logs : Logs by email', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let allEmails: MailDevEmail[];
  let numberOfEmails: number;
  let mailListener: MailDev;
  // Data to create product
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    taxRule: 'No tax',
    quantity: 1,
  });

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(`${baseContext}_preTest_1`);

  // Pre-condition : Create product
  createProductTest(productData, `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

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

  describe('Logs by email', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > Logs\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToEraseLogs', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.advancedParametersLink, boDashboardPage.logsLink);
      await boLogsPage.closeSfToolBar(page);

      const pageTitle = await boLogsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLogsPage.pageTitle);
    });

    it('should enter an invalid email in \'Send emails to\' input and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setInvalidEmail', baseContext);

      const errorMessage = await boLogsPage.setEmail(page, 'demo@prestashop.');
      expect(errorMessage).to.eq('Invalid email: demo@prestashop..');
    });

    it('should enter a valid email in \'Send emails to\' input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setValidEmail', baseContext);

      const errorMessage = await boLogsPage.setEmail(page, dataEmployees.defaultEmployee.email);
      expect(errorMessage).to.eq(boLogsPage.successfulUpdateMessage);
    });

    it('should choose \'Informative Only\' in minimum severity level', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseSeverity', baseContext);

      const errorMessage = await boLogsPage.setMinimumSeverityLevel(page, 'Informative only');
      expect(errorMessage).to.eq(boLogsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should go to the first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditPage', baseContext);

      await boProductsPage.goToProductPage(page, 1);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should edit the product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductNameEn', baseContext);

      await boProductsCreatePage.setProductName(page, faker.commerce.productName(), 'en');

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check the confirmation email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkConfirmationEmail', baseContext);

      numberOfEmails = allEmails.length;
      expect(allEmails[numberOfEmails - 1].subject)
        .to.equal(`[${global.INSTALL.SHOP_NAME}] Log: You have a new alert from your store`);
    });
  });

  describe('Delete created product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(`${baseContext}_postTest`);
});
