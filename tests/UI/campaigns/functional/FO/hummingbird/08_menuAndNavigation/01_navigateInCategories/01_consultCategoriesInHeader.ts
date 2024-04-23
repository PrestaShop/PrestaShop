// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';

// Import data
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import CategoryData from '@data/faker/category';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigateInCategories_consultCategoriesInHeader';

/*
Go to FO
Check all categories and subcategories links in header
 */

describe('FO - Menu and Navigation : Check categories and subcategories links in header', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  describe('Check categories and subcategories links in header', async () => {
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
      expect(isHomePage).to.eq(true);
    });

    [Categories.clothes, Categories.accessories, Categories.art].forEach((test: CategoryData) => {
      it(`should check category '${test.name}' link`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.name}Link`, baseContext);

        await homePage.goToCategory(page, test.id);

        const pageTitle = await homePage.getPageTitle(page);
        expect(pageTitle).to.equal(test.name);
      });
    });

    [
      {args: {category: Categories.clothes, subcategory: Categories.men}},
      {args: {category: Categories.clothes, subcategory: Categories.women}},
      {args: {category: Categories.accessories, subcategory: Categories.stationery}},
      {args: {category: Categories.accessories, subcategory: Categories.homeAccessories}},
    ].forEach((test) => {
      it(`should check subcategory '${test.args.subcategory.name}' link`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.subcategory.name}Link`, baseContext);

        await homePage.goToSubCategory(page, test.args.category.id, test.args.subcategory.id);

        const pageTitle = await homePage.getPageTitle(page);
        expect(pageTitle).to.equal(test.args.subcategory.name);
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
