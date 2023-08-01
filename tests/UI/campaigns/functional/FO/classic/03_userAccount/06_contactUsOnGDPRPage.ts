// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import customerServicePage from '@pages/BO/customerService/customerService';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {contactUsPage} from '@pages/FO/contactUs';
import {homePage} from '@pages/FO/home';
import {loginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';
import gdprPersonalDataPage from '@pages/FO/myAccount/gdprPersonalData';

// Import demo data
import Customers from '@data/demo/customers';
import Orders from '@data/demo/orders';
import MessageData from '@data/faker/message';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_userAccount_contactUsOnGDPRPage';

describe('FO - Account : Contact us on GDPR page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: MessageData = new MessageData({
    firstName: Customers.johnDoe.firstName,
    lastName: Customers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: Customers.johnDoe.email,
    reference: Orders.firstOrder.reference,
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

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

    await homePage.goToLoginPage(page);

    const pageHeaderTitle = await loginPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
  });

  it('should sign in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

    await loginPage.customerLogin(page, Customers.johnDoe);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

    await homePage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should go to \'GDPR - Personal data\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage', baseContext);

    await myAccountPage.goToMyGDPRPersonalDataPage(page);

    const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
    await expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
  });

  it('should click on \'Contact page\' link from Rectification & Erasure requests block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

    await gdprPersonalDataPage.goToContactUsPage(page);

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(contactUsPage.pageTitle);
  });

  it('should send message to customer service', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

    const validationMessage = await contactUsPage.getAlertSuccess(page);
    await expect(validationMessage).to.equal(contactUsPage.validationMessage);
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
