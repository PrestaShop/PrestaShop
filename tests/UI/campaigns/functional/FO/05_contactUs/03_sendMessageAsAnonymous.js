require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');
const files = require('@utils/files');

// Import pages
// BO
const dashboardPage = require('@pages/BO/dashboard');
const customerServicePage = require('@pages/BO/customerService/customerService');

// FO
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foContactUsPage = require('@pages/FO/contactUs');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_contactUs_sendMessageAsAnonymous';

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');
const {DefaultCustomer} = require('@data/demo/customer');

const contactUsEmptyEmail = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: '',
  },
);
const contactUsInvalidEmail = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: 'demo@prestashop',
  },
);
const contactUsEmptyContent = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: DefaultCustomer.email,
    message: '',
  },
);
const contactUsData = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: DefaultCustomer.email,
  },
);

let browserContext;
let page;

/*
Go to FO
Check if not connected
Check errors
Send a message on contact page
Go to BO
Verify message on customer service page
 */
describe('FO - Contact us : Send message from contact us page with customer not logged', async () => {
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

      const pageTitle = await foContactUsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foContactUsPage.pageTitle);
    });

    it('should check if the email is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyEmail', baseContext);

      await foContactUsPage.sendMessage(page, contactUsEmptyEmail);

      const invalidEmailError = await foContactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(foContactUsPage.invalidEmail);
    });

    it('should check if the email is invalid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkInvalidEmail', baseContext);

      await foContactUsPage.sendMessage(page, contactUsInvalidEmail);

      const invalidEmailError = await foContactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(foContactUsPage.invalidEmail);
    });

    it('should check if the content is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEmptyContent', baseContext);

      await foContactUsPage.sendMessage(page, contactUsEmptyContent);

      const invalidEmailError = await foContactUsPage.getAlertError(page);
      await expect(invalidEmailError).to.contains(foContactUsPage.invalidContent);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

      const validationMessage = await foContactUsPage.getAlertSuccess(page);
      await expect(validationMessage).to.equal(foContactUsPage.validationMessage);
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
