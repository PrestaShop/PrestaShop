require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const homePage = require('@pages/FO/home');
const contactUsPage = require('@pages/FO/contactUs');
const customerServicePage = require('@pages/BO/customerService/customerService');
const viewPage = require('@pages/BO/customerService/customerService/view');

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_customerService_contactOptions';

let browserContext;
let page;
const contactUsData = new ContactUsFakerData({subject: 'Customer service', reference: ''});

/*
Disable Allow file uploading
Enable Allow file uploading
Update default message
 */
describe('Contact options', async () => {
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

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  describe('Enable/Disable allow file uploading', async () => {
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

    const tests = [
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ];

    tests.forEach((test, index) => {
      it(`should ${test.args.action} Allow file uploading`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}FileUploading`, baseContext);

        const result = await customerServicePage.allowFileUploading(page, test.args.enable);
        await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
      });

      it('should check the existence of attachment input in contact us form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUploadFile${index}`, baseContext);

        page = await customerServicePage.viewMyShop(page);

        await homePage.clickOnHeaderLink(page, 'Contact us');

        const pageTitle = await contactUsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(contactUsPage.pageTitle);

        const isVisible = await contactUsPage.isAttachmentInputVisible(page);
        await expect(isVisible).to.be.equal(test.args.enable);

        page = await contactUsPage.closePage(browserContext, page, 0);
      });
    });
  });

  describe('Update default message', async () => {
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

      const validationMessage = await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);
      await expect(validationMessage).to.equal(contactUsPage.validationMessage);

      page = await contactUsPage.closePage(browserContext, page, 0);
    });

    it('should update default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDefaultMessage', baseContext);

      const result = await customerServicePage.setDefaultMessage(page, 'test');
      await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
    });

    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await customerServicePage.goToViewMessagePage(page);

      const pageTitle = await viewPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewPage.pageTitle);
    });

    it('should check your answer form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkYourAnswerForm', baseContext);

      const formContent = await viewPage.getYourAnswerFormContent(page);
      expect(formContent).to.contains('test');
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

    it('should go back to default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDefaultMessage', baseContext);

      const result = await customerServicePage.setDefaultMessage(page, 'Dear Customer\n\n Regards,\nCustomer service');
      await expect(result).to.contains(customerServicePage.successfulUpdateMessage);
    });
  });

  describe('Delete the order message', async () => {
    it('should go to customer service page', async function () {
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

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.customerServiceLink,
      );

      const textResult = await customerServicePage.deleteMessage(page, 1);
      await expect(textResult).to.contains(customerServicePage.successfulDeleteMessage);
    });
  });
});
