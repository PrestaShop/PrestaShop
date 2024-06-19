// Import utils
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataProducts,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_shareLinks';

describe('FO - Product page - Product page : Share links', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.equal(true);
  });

  it(`should search for the product '${dataProducts.demo_12.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchDemo12', baseContext);

    await homePage.searchProduct(page, dataProducts.demo_12.name);

    const pageTitle = await searchResultsPage.getPageTitle(page);
    expect(pageTitle).to.equal(searchResultsPage.pageTitle);
  });

  it('should go to the product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageDemo12', baseContext);

    await searchResultsPage.goToProductPage(page, 1);

    const pageTitle = await productPage.getPageTitle(page);
    expect(pageTitle).to.contains(dataProducts.demo_12.name);
  });

  [
    {socialNetwork: 'Facebook', link: 'www.facebook.com', url: 'www.facebook.com'},
    {socialNetwork: 'Twitter', link: 'twitter.com', url: 'x.com'},
    {socialNetwork: 'Pinterest', link: 'www.pinterest.com', url: 'www.pinterest.com'},
  ].forEach((args) => {
    it(`should click on the ${args.socialNetwork} link and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `click${args.socialNetwork}Link`, baseContext);

      const socialLink = await productPage.getSocialSharingLink(page, args.socialNetwork);
      expect(socialLink).to.contains(args.link);

      page = await productPage.clickOnSocialSharingLink(page, args.socialNetwork);

      const link = await productPage.getCurrentURL(page);
      expect(link).to.contains(args.url);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `close${args.link}Page`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_12.name);
    });
  });
});
