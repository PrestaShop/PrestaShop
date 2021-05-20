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

const baseContext = 'functional_BO_customerService_customerService_contactOption';

let browserContext;
let page;
const contactUsData = new ContactUsFakerData({subject: 'Customer service', reference: 'OHSATSERP'});

/*
Disable Allow file uploading
Enable Allow file uploading
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
});
