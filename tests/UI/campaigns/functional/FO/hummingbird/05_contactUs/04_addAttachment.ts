// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerServicePage from '@pages/BO/customerService/customerService';
import viewPage from '@pages/BO/customerService/customerService/view';

// Import FO pages
import contactUsPage from '@pages/FO/hummingbird/contactUs';
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';

// Import data
import Customers from '@data/demo/customers';
import Orders from '@data/demo/orders';
import MessageData from '@data/faker/message';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_contactUs_addAttachment';

/*
Pre-condition:
- Install hummingbird theme
Scenario:
Go to FO
- Log in with default customer
- Try to send a message on contact page with csv attachment
- Send a message on contact page with png attachment
- Verify message and attachment on view customer service page
Post-condition:
- Uninstall hummingbird theme
 */
describe('FO - Contact us : Add attachment', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idCustomer: string;
  let messageDateTime: string;

  const contactUsData: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: Customers.johnDoe.email,
    reference: Orders.firstOrder.reference,
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.createFile('.', `${contactUsData.fileName}.csv`, 'new filename');
    await files.createFile('.', `${contactUsData.fileName}.png`, 'new filename');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(`${contactUsData.fileName}.csv`);
    await files.deleteFile(`${contactUsData.fileName}.png`);
  });

  describe('Add attachment', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFo', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await loginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(loginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFo', baseContext);

      await loginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await loginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await loginPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should try to send message with csv file to customer service and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendCSVFile', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.csv`);

      const validationMessage = await contactUsPage.getAlertError(page);
      expect(validationMessage).to.equal(contactUsPage.badFileExtensionErrorMessage);
    });

    it('should send message with PNG file to customer service and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendPNGFile', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.png`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should check customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'customer');
      expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'a!email');
      expect(email).to.contain(contactUsData.emailAddress);
    });

    it('should get the customer service id and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getMessageID', baseContext);

      idCustomer = await customerServicePage.getTextColumn(page, 1, 'id_customer_thread');
      expect(parseInt(idCustomer, 10)).to.be.at.least(0);

      messageDateTime = await customerServicePage.getTextColumn(page, 1, 'date');
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should check the thread form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThreadForm', baseContext);

      const text = await viewPage.getCustomerMessage(page);
      expect(text)
        .to.contains(contactUsData.emailAddress)
        .and.to.contains(contactUsData.subject)
        .and.to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`)
        .and.to.contains('Attachment')
        .and.to.contains(contactUsData.message);
    });

    it('should check the file attached', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFileAttached', baseContext);

      const fileExtension = await viewPage.getAttachedFileHref(page);
      expect(fileExtension).to.contains('.png');
    });

    it('should go back to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrderMessagesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
