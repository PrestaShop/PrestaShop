require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customerServicePage = require('@pages/BO/customerService/customerService');
const viewPage = require('@pages/BO/customerService/customerService/view');

// Import FO pages
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const contactUsPage = require('@pages/FO/contactUs');

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');
const {DefaultCustomer} = require('@data/demo/customer');

const baseContext = 'functional_BO_customerService_customerService_changeStatus';

let browserContext;
let page;
const contactUsData = new ContactUsFakerData({subject: 'Customer service', reference: 'OHSATSERP'});

/*
Send message by customer to customer service in FO
Change message status in BO
 */
describe('BO - Customer Service : Change status', async () => {
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

  describe('Send message to customer service', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await homePage.goToLoginPage(page);
      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to contact us page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToContactPage', baseContext);

      // Go to contact us page
      await homePage.goToFooterLink(page, 'Contact us');

      const pageTitle = await contactUsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(contactUsPage.pageTitle);
    });

    it('should send message to customer service', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

      const validationMessage = await contactUsPage.sendMessage(page, contactUsData, `${contactUsData.fileName}.jpg`);
      await expect(validationMessage).to.equal(contactUsPage.validationMessage);
    });
  });

  describe('Change message status and check it', async () => {
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
    [
      {args: {status: 'Handled', statusToCheck: 'Re-open'}},
      {args: {status: 'Re-open', statusToCheck: 'Mark as "handled"'}},
      {args: {status: 'Pending 1', statusToCheck: 'Disable pending status'}},
      {args: {status: 'Pending 2', statusToCheck: 'Disable pending status'}},
    ].forEach((test, index) => {
      it('should go to view message page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToViewMessagePage${index}`, baseContext);

        await customerServicePage.goToViewMessagePage(page);

        const pageTitle = await viewPage.getPageTitle(page);
        await expect(pageTitle).to.contains(viewPage.pageTitle);
      });

      it(`should change the order status to '${test.args.status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setOrderStatus${test.args.status}`, baseContext);

        const newStatus = await viewPage.setStatus(page, test.args.status);
        await expect(newStatus).to.contains(test.args.statusToCheck);
      });

      it('should go to \'Customer Service > Customer Service\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToOrderMessagesPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.customerServiceParentLink,
          dashboardPage.customerServiceLink,
        );

        const pageTitle = await customerServicePage.getPageTitle(page);
        await expect(pageTitle).to.contains(customerServicePage.pageTitle);
      });

      it('should check if the status color is changed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStatusColor${index}`, baseContext);

        const isChanged = await customerServicePage.isStatusChanged(page, 1, test.args.status);
        await expect(isChanged).to.be.true;
      });
    });
  });

  describe('Delete the order message', async () => {
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
