import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataOrders,
  FakerContactMessage,
  foHummingbirdContactUsPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyGDPRPersonalDataPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_contactUsOnGDPRPage';

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

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.createFile('.', `${contactUsData.fileName}.txt`, 'new filename');
  });

  describe('Contact us on GDPR page', async () => {
    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);

      await utilsFile.deleteFile(`${contactUsData.fileName}.txt`);
    });

    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to my account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to \'GDPR - Personal data\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage', baseContext);

      await foHummingbirdMyAccountPage.goToMyGDPRPersonalDataPage(page);

      const pageTitle = await foHummingbirdMyGDPRPersonalDataPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdMyGDPRPersonalDataPage.pageTitle);
    });

    it('should click on \'Contact page\' link from Rectification & Erasure requests block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

      await foHummingbirdMyGDPRPersonalDataPage.goToContactUsPage(page);

      const pageTitle = await foHummingbirdContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdContactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foHummingbirdContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.txt`);

      const validationMessage = await foHummingbirdContactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(foHummingbirdContactUsPage.validationMessage);
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

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
