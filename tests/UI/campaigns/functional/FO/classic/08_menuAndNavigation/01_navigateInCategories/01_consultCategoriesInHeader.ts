// Import utils
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataCategories,
  FakerCategory,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigateInCategories_consultCategoriesInHeader';

/*
Go to FO
Check all categories and subcategories links in header
 */

describe('FO - Menu and Navigation : Check categories and subcategories links in header', async () => {
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

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  [dataCategories.clothes, dataCategories.accessories, dataCategories.art].forEach((test: FakerCategory) => {
    it(`should check category '${test.name}' link`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `check${test.name}Link`, baseContext);

      await homePage.goToCategory(page, test.id);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(test.name);
    });
  });

  [
    {args: {category: dataCategories.clothes, subcategory: dataCategories.men}},
    {args: {category: dataCategories.clothes, subcategory: dataCategories.women}},
    {args: {category: dataCategories.accessories, subcategory: dataCategories.stationery}},
    {args: {category: dataCategories.accessories, subcategory: dataCategories.homeAccessories}},
  ].forEach((test) => {
    it(`should check subcategory '${test.args.subcategory.name}' link`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `check${test.args.subcategory.name}Link`, baseContext);

      await homePage.goToSubCategory(page, test.args.category.id, test.args.subcategory.id);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(test.args.subcategory.name);
    });
  });
});
