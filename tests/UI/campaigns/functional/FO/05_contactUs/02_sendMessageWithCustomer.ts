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
import foContactUsPage from '@pages/FO/contactUs';
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';

// Import data
import {DefaultCustomer} from '@data/demo/customer';
import {Orders} from '@data/demo/orders';
import ContactUsFakerData from '@data/faker/contactUs';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_contactUs_sendMessageWithCustomer';

/*
Go to FO
Log in with default customer
Send a message on contact page
Verify message on customer service page
 */
describe('FO - Contact us : Send message from contact us page with customer logged in', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: ContactUsFakerData = new ContactUsFakerData({
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: DefaultCustomer.email,
    reference: Orders.firstOrder.ref,
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

    await foContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

    const validationMessage = await foContactUsPage.getAlertSuccess(page);
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
