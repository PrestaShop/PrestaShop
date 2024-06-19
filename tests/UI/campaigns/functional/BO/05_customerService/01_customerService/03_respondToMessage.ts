// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {resetSmtpConfigTest, setupSmtpConfigTest} from '@commonTests/BO/advancedParameters/smtp';

// Import pages
// Import BO pages
import customerServicePage from '@pages/BO/customerService/customerService';
import viewPage from '@pages/BO/customerService/customerService/view';
// Import FO pages
import {contactUsPage} from '@pages/FO/classic/contactUs';
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerContactMessage,
  type MailDev,
  type MailDevEmail,
  utilsFile,
  utilsMail,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_customerService_respondToMessage';

/*
Pre-condition:
- Setup SMTP parameters
Scenario:
- Send message by customer to customer service in FO
- Respond to message in BO
- Check received email
- Delete message
Post-condition:
- Reset SMTP parameters
 */
describe('BO - Customer Service : Respond to message', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let newMail: MailDevEmail;
  let mailListener: MailDev;

  const contactUsData: FakerContactMessage = new FakerContactMessage({subject: 'Customer service'});
  const answerMessage = 'My response';

  // Pre-Condition : Setup config SMTP
  setupSmtpConfigTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Start listening to maildev server
    mailListener = utilsMail.createMailListener();
    utilsMail.startListener(mailListener);

    // Handle every new email
    mailListener.on('new', (email: MailDevEmail) => {
      newMail = email;
    });

    await utilsFile.generateImage(`${contactUsData.fileName}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Stop listening to maildev server
    utilsMail.stopListener(mailListener);

    await utilsFile.deleteFile(`${contactUsData.fileName}.jpg`);
  });

  describe('FO : Send message', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

      // Go to contact us page
      await homePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });
  });

  describe('BO : Respond to message', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrderMessagesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should add a response and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'respondToMessage', baseContext);

      await viewPage.addResponse(page, answerMessage);

      const messages = await viewPage.getThreadMessages(page);
      expect(messages).to.contains(answerMessage);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm', baseContext);

      const text = await viewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains(answerMessage);
    });

    it('should check that the confirmation mail is in mailbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMail', baseContext);

      expect(newMail.subject).to.contains(`[${global.INSTALL.SHOP_NAME}] An answer to your message`);
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

      const pageTitle = await customerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });

  // Post-Condition : Reset SMTP config
  resetSmtpConfigTest(baseContext);
});
