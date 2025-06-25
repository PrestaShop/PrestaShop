import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataOrders,
  FakerContactMessage,
  foClassicContactUsPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyGDPRPersonalDataPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_userAccount_contactUsOnGDPRPage';

describe('FO - Account : Contact us on GDPR page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: FakerContactMessage = new FakerContactMessage({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    subject: 'Customer service',
    emailAddress: dataCustomers.johnDoe.email,
    reference: dataOrders.order_1.reference,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(`${contactUsData.fileName}.txt`);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageHeaderTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageHeaderTitle).to.equal(foClassicLoginPage.pageTitle);
  });

  it('should sign in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
  });

  it('should go to \'GDPR - Personal data\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage', baseContext);

    await foClassicMyAccountPage.goToMyGDPRPersonalDataPage(page);

    const pageTitle = await foClassicMyGDPRPersonalDataPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyGDPRPersonalDataPage.pageTitle);
  });

  it('should click on \'Contact page\' link from Rectification & Erasure requests block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

    await foClassicMyGDPRPersonalDataPage.goToContactUsPage(page);

    const pageTitle = await foClassicContactUsPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
  });

  it('should send message to customer service', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    await foClassicContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

    const validationMessage = await foClassicContactUsPage.getAlertSuccess(page);
    expect(validationMessage).to.equal(foClassicContactUsPage.validationMessage);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to customer service page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customerServiceParentLink,
      boDashboardPage.customerServiceLink,
    );

    const pageTitle = await boCustomerServicePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
  });

  it('should check message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMessage', baseContext);

    const message = await boCustomerServicePage.getTextColumn(page, 1, 'message');
    expect(message).to.contain(contactUsData.message);
  });

  it('should delete the message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

    const textResult = await boCustomerServicePage.deleteMessage(page, 1);
    expect(textResult).to.contains(boCustomerServicePage.successfulDeleteMessage);
  });
});
