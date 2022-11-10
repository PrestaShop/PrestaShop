// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

require('module-alias/register');

const {expect} = require('chai');

// Import pages
const homePage = require('@pages/FO/home');
const contactUsPage = require('@pages/FO/contactUs');

// Import data
const {DefaultEmployee} = require('@data/demo/employees');

const baseContext = 'functional_FO_contactUs_checkMailtoLink';

let browserContext;
let page;

/*
Go to FO
Click on contact us link
Check email us link
 */
describe('FO - Contact us : Check mail link on contact us page', async () => {
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

  it('should go to \'Contact us\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

    await homePage.clickOnHeaderLink(page, 'Contact us');

    const pageTitle = await contactUsPage.getPageTitle(page);
    await expect(pageTitle, 'Fail to open FO login page').to.contains(contactUsPage.pageTitle);
  });

  it('should check email us link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEmailUsLink', baseContext);

    const emailUsLinkHref = await contactUsPage.getEmailUsLink(page);
    await expect(emailUsLinkHref).to.equal(`mailto:${DefaultEmployee.email}`);
  });
});
