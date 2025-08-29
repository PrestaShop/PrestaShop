import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerServicePage,
  boCustomerServiceViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerContactMessage,
  foClassicContactUsPage,
  foClassicHomePage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_customerService_viewMessage';

/*
Send message by customer to customer service in FO
View customer message in BO
 */
describe('BO - Customer Service : View message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idCustomer: string = '0';
  let messageDateTime: string = '';

  const contactUsData: FakerContactMessage = new FakerContactMessage({subject: 'Customer service'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(`${contactUsData.fileName}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(`${contactUsData.fileName}.jpg`);
  });

  describe('FO : Send message', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await foClassicHomePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foClassicContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);

      const validationMessage = await foClassicContactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(foClassicContactUsPage.validationMessage);
    });
  });

  describe('BO : View message', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
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

      const badgeNumber = await boCustomerServiceViewPage.getBadgeNumber(page);
      expect(badgeNumber).to.contains(idCustomer);

      const text = await boCustomerServiceViewPage.getCustomerMessage(page);
      expect(text).to.contains(contactUsData.emailAddress);
      expect(text).to.contains(contactUsData.subject);
      expect(text).to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`);
      expect(text).to.contains('Attachment');
      expect(text).to.contains(contactUsData.message);
    });

    it('should check your answer form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkYourAnswerForm', baseContext);

      // Check that title has email on it
      const titleContent = await boCustomerServiceViewPage.getYourAnswerFormTitle(page);
      expect(titleContent).to.contains(`Your answer to ${contactUsData.emailAddress}`);

      // Check form content
      const formContent = await boCustomerServiceViewPage.getYourAnswerFormContent(page);
      expect(formContent).to.contains('Dear Customer, Regards, Customer service');
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm', baseContext);

      const text = await boCustomerServiceViewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline');
      expect(text).to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`);
      expect(text).to.contains(`Message to: ${contactUsData.subject}`);
      expect(text).to.contains(contactUsData.message);
    });
  });

  describe('BO : Delete the message', async () => {
    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPageToDelete', baseContext);

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
});
