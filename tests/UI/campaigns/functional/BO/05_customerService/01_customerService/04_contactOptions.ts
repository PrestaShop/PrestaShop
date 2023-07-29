// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

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

const baseContext: string = 'functional_BO_customerService_customerService_contactOptions';

/*
Update default message
Disable Allow file uploading
Enable Allow file uploading
 */
describe('BO - Customer Service : Contact options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: MessageData = new MessageData({subject: 'Customer service', reference: ''});

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

  describe('BO : Update default message', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const pageTitle = await customerServicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerServicePage.pageTitle);
    });

    it('should update default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDefaultMessage', baseContext);

      const result = await customerServicePage.setDefaultMessage(page, 'Test default message');
      await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
    });
  });

  describe('FO : Send message', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrder', baseContext);

      page = await customerServicePage.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

      // Go to contact us page
      await homePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should send message to customer service then close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);

      const validationMessage = await contactUsPage.getAlertSuccess(page);
      await expect(validationMessage).to.equal(contactUsPage.validationMessage);

      page = await contactUsPage.closePage(browserContext, page, 0);
    });
  });

  describe('BO : Check default message', async () => {
    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.reloadPage(page);
      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should check your answer form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkYourAnswerForm', baseContext);

      const formContent = await viewPage.getYourAnswerFormContent(page);
      expect(formContent).to.contains('Test default message');
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

    it('should go back to default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDefaultMessage', baseContext);

      const result = await customerServicePage.setDefaultMessage(page, 'Dear Customer,\n\n Regards,\nCustomer service');
      await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
    });
  });

  describe('BO/FO : Enable/Disable allow file uploading and check in FO', async () => {
    [
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ].forEach((test, index: number) => {
      it(`should ${test.args.action} Allow file uploading`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}FileUploading`, baseContext);

        const result = await customerServicePage.allowFileUploading(page, test.args.enable);
        await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await customerServicePage.viewMyShop(page);

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should go to contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToContactUsPage${index}`, baseContext);

        await homePage.clickOnHeaderLink(page, 'Contact us');

        const pageTitle = await contactUsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(contactUsPage.pageTitle);
      });

      it('should check the existence of attachment input in contact us form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUploadFile${index}`, baseContext);

        const isVisible = await contactUsPage.isAttachmentInputVisible(page);
        await expect(isVisible).to.be.equal(test.args.enable);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await contactUsPage.closePage(browserContext, page, 0);

        const pageTitle = await customerServicePage.getPageTitle(page);
        await expect(pageTitle).to.contains(customerServicePage.pageTitle);
      });
    });
  });

  describe('BO : Delete the order message', async () => {
    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await customerServicePage.deleteMessage(page, 1);
      await expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
