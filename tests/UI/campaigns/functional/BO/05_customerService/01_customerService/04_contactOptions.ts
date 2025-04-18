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

const baseContext: string = 'functional_BO_customerService_customerService_contactOptions';

/*
Update default message
Disable Allow file uploading
Enable Allow file uploading
 */
describe('BO - Customer Service : Contact options', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contactUsData: FakerContactMessage = new FakerContactMessage({subject: 'Customer service', reference: ''});

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

  describe('BO : Update default message', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Customer Service\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerServicePage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.customerServiceLink,
      );

      const pageTitle = await boCustomerServicePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
    });

    it('should update default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateDefaultMessage', baseContext);

      const result = await boCustomerServicePage.setDefaultMessage(page, 'Test default message');
      expect(result).to.contains(boCustomerServicePage.successfulUpdateMessage);
    });
  });

  describe('FO : Send message', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToOrder', baseContext);

      page = await boCustomerServicePage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

      // Go to contact us page
      await foClassicHomePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await foClassicContactUsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
    });

    it('should send message to customer service then close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      await foClassicContactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);

      const validationMessage = await foClassicContactUsPage.getAlertSuccess(page);
      expect(validationMessage).to.equal(foClassicContactUsPage.validationMessage);

      page = await foClassicContactUsPage.closePage(browserContext, page, 0);
    });
  });

  describe('BO : Check default message', async () => {
    it('should go to view message page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewMessagePage', baseContext);

      await boCustomerServicePage.reloadPage(page);
      await boCustomerServicePage.goToViewMessagePage(page);

      const pageTitle = await boCustomerServiceViewPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerServiceViewPage.pageTitle);
    });

    it('should check your answer form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkYourAnswerForm', baseContext);

      const formContent = await boCustomerServiceViewPage.getYourAnswerFormContent(page);
      expect(formContent).to.contains('Test default message');
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

    it('should go back to default message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDefaultMessage', baseContext);

      const result = await boCustomerServicePage.setDefaultMessage(page, 'Dear Customer,\n\n Regards,\nCustomer service');
      expect(result).to.contains(boCustomerServicePage.successfulUpdateMessage);
    });
  });

  describe('BO/FO : Enable/Disable allow file uploading and check in FO', async () => {
    [
      {args: {action: 'disable', enable: false}},
      {args: {action: 'enable', enable: true}},
    ].forEach((test, index: number) => {
      it(`should ${test.args.action} Allow file uploading`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}FileUploading`, baseContext);

        const result = await boCustomerServicePage.allowFileUploading(page, test.args.enable);
        expect(result).to.contains(boCustomerServicePage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

        page = await boCustomerServicePage.viewMyShop(page);

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToContactUsPage${index}`, baseContext);

        await foClassicHomePage.clickOnHeaderLink(page, 'Contact us');

        const pageTitle = await foClassicContactUsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
      });

      it('should check the existence of attachment input in contact us form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkUploadFile${index}`, baseContext);

        const isVisible = await foClassicContactUsPage.isAttachmentInputVisible(page);
        expect(isVisible).to.be.equal(test.args.enable);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        page = await foClassicContactUsPage.closePage(browserContext, page, 0);

        const pageTitle = await boCustomerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
      });
    });
  });

  describe('BO : Delete the order message', async () => {
    it('should delete the message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteMessage', baseContext);

      const textResult = await boCustomerServicePage.deleteMessage(page, 1);
      expect(textResult).to.contains(boCustomerServicePage.successfulDeleteMessage);
    });
  });
});
