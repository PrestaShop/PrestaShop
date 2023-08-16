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
import {loginPage as foLoginPage} from '@pages/FO/login';

// Import data
import Customers from '@data/demo/customers';
import MessageData from '@data/faker/message';
import Employees from '@data/demo/employees';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customerService_customerService_changeStatus';

/*
Send message by customer to customer service in FO
Change message status in BO
 */
describe('BO - Customer Service : Change status', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: MessageData = new MessageData({subject: 'Customer service', reference: 'OHSATSERP'});

  const forwardMessageData: MessageData = new MessageData({
    employeeName: `${Employees.DefaultEmployee.firstName.slice(0, 1)}. ${Employees.DefaultEmployee.lastName}`,
    message: 'Forward message',
  });

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

  describe('FO : Send message to customer service', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, Customers.johnDoe);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

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

  describe('BO : Change message status and check it', async () => {
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
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });
    [
      {args: {status: 'Handled', statusToCheck: 'Re-open'}},
      {args: {status: 'Re-open', statusToCheck: 'Mark as "handled"'}},
      {args: {status: 'Pending 1', statusToCheck: 'Disable pending status'}},
      {args: {status: 'Pending 2', statusToCheck: 'Disable pending status'}},
    ].forEach((test, index: number) => {
      it('should go to view message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToViewMessagePage${index}`, baseContext);

        await customerServicePage.goToViewMessagePage(page);

        const pageTitle = await viewPage.getPageTitle(page);
        expect(pageTitle).to.contains(viewPage.pageTitle);
      });

      it(`should change the order status to '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setOrderStatus${test.args.status}`, baseContext);

        const newStatus = await viewPage.setStatus(page, test.args.status);
        expect(newStatus).to.contains(test.args.statusToCheck);
      });

      it('should go to \'Customer Service > Customer Service\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderMessagesPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.customerServiceLink,
        );

        const pageTitle = await customerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(customerServicePage.pageTitle);
      });

      it('should check if the status color is changed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStatusColor${index}`, baseContext);

        const isChanged = await customerServicePage.isStatusChanged(page, 1, test.args.status);
        expect(isChanged).to.eq(true);
      });
    });
  });

  describe('BO : Forward the message to an existing employee', async () => {
    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton', baseContext);

      const isModalVisible = await viewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage', baseContext);

      await viewPage.forwardMessage(page, forwardMessageData);

      const messages = await viewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`${viewPage.forwardMessageSuccessMessage} ${Employees.DefaultEmployee.firstName}`
          + ` ${Employees.DefaultEmployee.lastName}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm', baseContext);

      const text = await viewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline')
        .and.contains(`${viewPage.forwardMessageSuccessMessage} ${Employees.DefaultEmployee.firstName}`
        + ` ${Employees.DefaultEmployee.lastName}`)
        .and.contains(`Comment: ${forwardMessageData.message}`);
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
      expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
