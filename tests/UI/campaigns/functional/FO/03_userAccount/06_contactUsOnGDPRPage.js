require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');

// Import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const gdprPersonalDataPage = require('@pages/FO/myAccount/gdprPersonalData');
const contactUsPage = require('@pages/FO/contactUs');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customerServicePage = require('@pages/BO/customerService/customerService');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Orders} = require('@data/demo/orders');

// Import faker data
const ContactUsFakerData = require('@data/faker/contactUs');

const baseContext = 'functional_FO_userAccount_contactUsOnGDPRPage';

let browserContext;
let page;

const contactUsData = new ContactUsFakerData(
  {
    firstName: DefaultCustomer.firstName,
    lastName: DefaultCustomer.lastName,
    subject: 'Customer service',
    emailAddress: DefaultCustomer.email,
    reference: Orders.firstOrder.ref,
  },
);

describe('FO - Account : Contact us on GDPR page', async () => {
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

    await loginPage.customerLogin(page, DefaultCustomer);
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
