require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const homePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const contactUsPage = require('@pages/FO/contactUs');
const customerServicePage = require('@pages/BO/customerService/customerService');
const viewPage = require('@pages/BO/customerService/customerService/view');

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_changeStatus';

let browserContext;
let page;
const contactUsData = new ContactUsFakerData({subject: 'Customer service', reference: 'OHSATSERP'});

/*
Send message by customer to customer service in FO
Change message status in BO
 */
describe('Change customer message status', async () => {
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
});
