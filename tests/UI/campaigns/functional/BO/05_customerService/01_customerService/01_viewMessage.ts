// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import customerServicePage from '@pages/BO/customerService/customerService';
import viewPage from '@pages/BO/customerService/customerService/view';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {contactUsPage} from '@pages/FO/contactUs';
import {homePage} from '@pages/FO/home';

// Import data
import MessageData from '@data/faker/message';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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

  const contactUsData: MessageData = new MessageData({subject: 'Customer service'});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage(`${contactUsData.fileName}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(`${contactUsData.fileName}.jpg`);
  });

  describe('FO : Send message', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await homePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      await expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });
  });

  describe('BO : View message', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should get the customer service id and the date', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getMessageID', baseContext);

      idCustomer = await customerServicePage.getTextColumn(page, 1, 'id_customer_thread');
      await expect(parseInt(idCustomer, 10)).to.be.at.least(0);

      messageDateTime = await customerServicePage.getTextColumn(page, 1, 'date');
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should check the thread form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThreadForm', baseContext);

      const badgeNumber = await viewPage.getBadgeNumber(page);
      await expect(badgeNumber).to.contains(idCustomer);

      const text = await viewPage.getCustomerMessage(page);
      expect(text).to.contains(contactUsData.emailAddress);
      expect(text).to.contains(contactUsData.subject);
      expect(text).to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`);
      expect(text).to.contains('Attachment');
      expect(text).to.contains(contactUsData.message);
    });

    it('should check your answer form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkYourAnswerForm', baseContext);

      // Check that title has email on it
      const titleContent = await viewPage.getYourAnswerFormTitle(page);
      expect(titleContent).to.contains(`Your answer to ${contactUsData.emailAddress}`);

      // Check form content
      const formContent = await viewPage.getYourAnswerFormContent(page);
      expect(formContent).to.contains('Dear Customer, Regards, Customer service');
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm', baseContext);

      const text = await viewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline');
      expect(text).to.contains(`${messageDateTime.substring(0, 10)} - ${messageDateTime.substring(11, 16)}`);
      expect(text).to.contains(`Message to: ${contactUsData.subject}`);
      expect(text).to.contains(contactUsData.message);
    });
  });

  describe('BO : Delete the message', async () => {
    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPageToDelete', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      await expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
