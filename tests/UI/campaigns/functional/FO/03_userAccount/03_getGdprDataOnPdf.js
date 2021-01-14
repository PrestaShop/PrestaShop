require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import data
const {DefaultAccount} = require('@data/demo/customer');

// Importing pages
const foHomePage = require('@pages/FO/home');
const foLoginPage = require('@pages/FO/login');
const foMyAccountPage = require('@pages/FO/myAccount');
const foPersonalGdprDataPage = require('@pages/FO/myAccount/personalGdprData');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_userAccount_getGdprDataOnPdf';

// Init const
let pdfFilePath;

let browserContext;
let page;

/*
Sign in with default account
Go to GDPR - Personal data page
Download GDPR data on a pdf file and check user information
 */
describe('Get GDPR data on pdf', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    // Delete downloaded files
    await Promise.all([
      files.deleteFile(pdfFilePath),
    ]);

    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foHomePage.goToFo(page);
    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

    await foHomePage.goToLoginPage(page);

    const pageHeaderTitle = await foLoginPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
  });

  it('Should sign in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

    await foLoginPage.customerLogin(page, DefaultAccount);
    const isCustomerConnected = await foMyAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should go to \'GDPR - Personal data\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoPersonalGdprDataPage', baseContext);

    await foMyAccountPage.goToPersonalGdprDataPage(page);
    const pageHeaderTitle = await foPersonalGdprDataPage.getHeaderTitle(page);
    await expect(pageHeaderTitle).to.equal(foPersonalGdprDataPage.formTitle);
  });

  it('should download pdf file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadPdfFile', baseContext);

    pdfFilePath = await foPersonalGdprDataPage.downloadPersonalDataOnPdf(page);
    const isPdfFileExist = await files.doesFileExist(pdfFilePath);
    await expect(isPdfFileExist).to.be.true;
  });

  it('should check account data on pdf file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkAccountData', baseContext);

    const isTextInPdf = await files.isTextInPDF(pdfFilePath, `${DefaultAccount.firstName} ${DefaultAccount.lastName}`);
    await expect(isTextInPdf).to.be.true;
  });
});
