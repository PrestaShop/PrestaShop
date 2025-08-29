import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerServicePage,
  boCustomerServiceViewPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  dataEmployees,
  FakerContactMessage,
  foClassicContactUsPage,
  foClassicHomePage,
  foClassicLoginPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_customerService_changeStatus';

/*
Send message by customer to customer service in FO
Change message status in BO
 */
describe('BO - Customer Service : Change status', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: FakerContactMessage = new FakerContactMessage({subject: 'Customer service', reference: 'OHSATSERP'});

  const forwardMessageData: FakerContactMessage = new FakerContactMessage({
    employeeName: `${dataEmployees.defaultEmployee.firstName.slice(0, 1)}. ${dataEmployees.defaultEmployee.lastName}`,
    message: 'Forward message',
  });

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

  describe('FO : Send message to customer service', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await foClassicHomePage.goTo(page, global.FO.URL);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

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

  describe('BO : Change message status and check it', async () => {
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
    [
      {args: {status: 'Handled', statusToCheck: 'Re-open'}},
      {args: {status: 'Re-open', statusToCheck: 'Mark as "handled"'}},
      {args: {status: 'Pending 1', statusToCheck: 'Disable pending status'}},
      {args: {status: 'Pending 2', statusToCheck: 'Disable pending status'}},
    ].forEach((test, index: number) => {
      it('should go to view message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToViewMessagePage${index}`, baseContext);

        await boCustomerServicePage.goToViewMessagePage(page);

        const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
      });

      it(`should change the order status to '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setOrderStatus${test.args.status}`, baseContext);

        const newStatus = await boCustomerServiceViewPage.setStatus(page, test.args.status);
        expect(newStatus).to.contains(test.args.statusToCheck);
      });

      it('should go to \'Customer Service > Customer Service\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderMessagesPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customerServiceParentLink,
          boDashboardPage.customerServiceLink,
        );

        const pageTitle = await boCustomerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
      });

      it('should check if the status color is changed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStatusColor${index}`, baseContext);

        const isChanged = await boCustomerServicePage.isStatusChanged(page, 1, test.args.status);
        expect(isChanged).to.eq(true);
      });
    });
  });

  describe('BO : Forward the message to an existing employee', async () => {
    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await boCustomerServicePage.goToViewMessagePage(page);

      const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
    });

    it('should click on forward message button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnForwardButton', baseContext);

      const isModalVisible = await boCustomerServiceViewPage.clickOnForwardMessageButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should forward the message and check the thread', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'forwardMessage', baseContext);

      await boCustomerServiceViewPage.forwardMessage(page, forwardMessageData);

      const messages = await boCustomerServiceViewPage.getThreadMessages(page);
      expect(messages)
        .to.contains(`${boCustomerServiceViewPage.forwardMessageSuccessMessage} ${dataEmployees.defaultEmployee.firstName}`
          + ` ${dataEmployees.defaultEmployee.lastName}`)
        .and.contains(forwardMessageData.message);
    });

    it('should check orders and messages timeline', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersAndMessagesForm', baseContext);

      const text = await boCustomerServiceViewPage.getOrdersAndMessagesTimeline(page);
      expect(text).to.contains('Orders and messages timeline')
        .and.contains(`${boCustomerServiceViewPage.forwardMessageSuccessMessage} ${dataEmployees.defaultEmployee.firstName}`
        + ` ${dataEmployees.defaultEmployee.lastName}`)
        .and.contains(`Comment: ${forwardMessageData.message}`);
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
