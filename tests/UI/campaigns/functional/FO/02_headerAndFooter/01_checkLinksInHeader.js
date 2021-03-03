require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Importing pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const contactUsPage = require('@pages/FO/contactUs');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_checkLinksInFooter';

let browserContext;
let page;

/*
Go to FO
Check header links
 */

describe('Check links in header page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should check \'Contact us\' footer links', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsFooterLinks', baseContext);

    // Check prices drop link
    await homePage.goToHeaderLink(page, 'Contact us');

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle).to.equal(contactUsPage.pageTitle);
  });
});
