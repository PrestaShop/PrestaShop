// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataEmployees,
  foClassicContactUsPage,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_contactUs_checkMailtoLink';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await foClassicHomePage.goToFo(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to \'Contact us\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToContactUsPage', baseContext);

    await foClassicHomePage.clickOnHeaderLink(page, 'Contact us');

    const pageTitle = await foClassicContactUsPage.getPageTitle(page);
    expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicContactUsPage.pageTitle);
  });

  it('should check email us link', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEmailUsLink', baseContext);

    const emailUsLinkHref = await foClassicContactUsPage.getEmailUsLink(page);
    expect(emailUsLinkHref).to.equal(`mailto:${dataEmployees.defaultEmployee.email}`);
  });
});
