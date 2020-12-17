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

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerService_orderMessages_viewCustomerMessage';

// Import data
const ContactUsFakerData = require('@data/faker/contactUs');

let browserContext;
let page;
const contactUsData = new ContactUsFakerData({subject: 'Customer service'});

/*
Send message by customer to customer service in FO
View customer message in BO
 */
describe('View customer service message', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

    await homePage.goTo(page, global.FO.URL);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go on contact us page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openShop', baseContext);

    // Go to contact us page
    await homePage.goToContactUsPage(page);

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(contactUsPage.pageTitle);
  });

  it('should send message to customer service', async function (){
    await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

    const validationMessage = await contactUsPage.sendMessage(page, contactUsData);
    await expect(validationMessage).to.equal(contactUsPage.validationMessage);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
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
});
