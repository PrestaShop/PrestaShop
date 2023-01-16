// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import contactUsPage from '@pages/FO/contactUs';
import homePage from '@pages/FO/home';

// Import data
import {DefaultEmployee} from '@data/demo/employees';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_contactUs_checkMailtoLink';

/*
Go to FO
Click on contact us link
Check email us link
 */
describe('FO - Contact us : Check mail link on contact us page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
