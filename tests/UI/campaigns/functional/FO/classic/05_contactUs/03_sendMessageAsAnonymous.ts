// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import customerServicePage from '@pages/BO/customerService/customerService';
// Import FO pages
import {contactUsPage} from '@pages/FO/contactUs';
import {homePage as foHomePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';

// Import data
import Customers from '@data/demo/customers';
import MessageData from '@data/faker/message';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_contactUs_sendMessageAsAnonymous';

/*
Go to FO
Check if not connected
Check errors
Send a message on contact page
Go to BO
Verify message on customer service page
 */
describe('FO - Contact us : Send message from contact us page with customer not logged', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsEmptyEmail: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: '',
  });
  const contactUsInvalidEmail: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: 'demo@prestashop',
  });
  const contactUsEmptyContent: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: Customers.johnDoe.email,
    message: '',
  });
  const contactUsData: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: Customers.johnDoe.email,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(`${contactUsData.fileName}.txt`);
  });

  describe('FO - Send message from contact us page with customer not logged', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check if that any account is connected', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkIfCustomerNotConnected', baseContext);

      const isCustomerConnected = await foHomePage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected!').to.be.false;
    });

    it('should go on contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await foLoginPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should check if the email is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyEmail', baseContext);

      await contactUsPage.sendMessage(page, contactUsEmptyEmail);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(contactUsPage.invalidEmail);
    });

    it('should check if the email is invalid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidEmail', baseContext);

      await contactUsPage.sendMessage(page, contactUsInvalidEmail);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(contactUsPage.invalidEmail);
    });

    it('should check if the content is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyContent', baseContext);

      await contactUsPage.sendMessage(page, contactUsEmptyContent);

      const invalidEmailError = await contactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(contactUsPage.invalidContent);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      await expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });
  });

  describe('BO - Check in Customer Service Page the received message and delete it', async () => {
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
      await expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should check customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'customer');
      await expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await customerServicePage.getTextColumn(page, 1, 'a!email');
      await expect(email).to.contain(contactUsData.emailAddress);
    });

    it('should check message type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessageType', baseContext);

      const subject = await customerServicePage.getTextColumn(page, 1, 'cl!id_contact');
      await expect(subject).to.contain(contactUsData.subject);
    });

    it('should check message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

      const message = await customerServicePage.getTextColumn(page, 1, 'message');
      await expect(message).to.contain(contactUsData.message);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      await expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
