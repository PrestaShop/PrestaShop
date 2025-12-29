import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  boCustomerServicePage,
  boCustomerServiceViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataOrders,
  FakerContactMessage,
  foHummingbirdContactUsPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    await utilsFile.createFile('.', `${contactUsData.fileName}.csv`, 'new filename');
    await utilsFile.createFile('.', `${contactUsData.fileName}.png`, 'new filename');
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(`${contactUsData.fileName}.csv`);
    await utilsFile.deleteFile(`${contactUsData.fileName}.png`);
  });

  describe('Add attachment', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foHummingbirdHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFo', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foHummingbirdLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foHummingbirdLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await foHummingbirdLoginPage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foHummingbirdContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdContactUsPage.pageTitle);
    });

    it('should try to send message with csv file to customer service and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendCSVFile', baseContext);

      await foHummingbirdContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.csv`);

      const validationMessage = await foHummingbirdContactUsPage.getAlertError(page);
      expect(validationMessage).to.equal(foHummingbirdContactUsPage.badFileExtensionErrorMessage);
    });

    it('should send message with PNG file to customer service and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendPNGFile', baseContext);

      await foHummingbirdContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.png`);

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

    it('should check customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerName', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'customer');
      expect(email).to.contain(`${contactUsData.firstName} ${contactUsData.lastName}`);
    });

    it('should check customer email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerEmail', baseContext);

      const email = await boCustomerServicePage.getTextColumn(page, 1, 'a!email');
      expect(email).to.contain(contactUsData.emailAddress);
    });

    it('should get the customer service id and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getMessageID', baseContext);

      idCustomer = await boCustomerServicePage.getTextColumn(page, 1, 'id_customer_thread');
      expect(parseInt(idCustomer, 10)).to.be.at.least(0);

      messageDateTime = await boCustomerServicePage.getTextColumn(page, 1, 'date');
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await boCustomerServicePage.goToViewMessagePage(page);

      const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
    });

    it('should check the thread form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThreadForm', baseContext);

      const text = await boCustomerServiceViewPage.getCustomerMessage(page);
      expect(text)
        .to.contains(contactUsData.emailAddress)
        .and.to.contains(contactUsData.subject)
        .and.to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`)
        .and.to.contains('Attachment')
        .and.to.contains(contactUsData.message);
    });

    it('should check the file attached', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFileAttached', baseContext);

      const fileExtension = await boCustomerServiceViewPage.getAttachedFileHref(page);
      expect(fileExtension).to.contains('.png');
    });

    it('should go back to customer service page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToOrderMessagesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
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
