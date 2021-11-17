require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
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

const baseContext = 'functional_FO_contactUs_sendMessageWithCustomer';

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');
const {DefaultCustomer} = require('@data/demo/customer');
const {Orders} = require('@data/demo/orders');

const contactUsData = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: DefaultCustomer.email,
    reference: Orders.firstOrder.ref,
  },
);

let browserContext;
let page;

/*
Go to FO
Log in with default customer
Send a message on contact page
Verify message on customer service page
 */
describe('FO - Contact us : Send message from contact us page with customer logged in', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

    await foHomePage.goTo(page, global.FO.URL);

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFo', baseContext);

    await foHomePage.goToLoginPage(page);
    const pageTitle = await foLoginPage.getPageTitle(page);
    await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sighInFo', baseContext);

    await foLoginPage.customerLogin(page, DefaultCustomer);
    const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should go on contact us page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

    // Go to contact us page
    await foLoginPage.goToFooterLink(page, 'Contact us');

    const pageTitle = await foContactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(foContactUsPage.pageTitle);
  });

  it('should send message to customer service', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    const validationMessage = await foContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);
    await expect(validationMessage).to.equal(foContactUsPage.validationMessage);
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
