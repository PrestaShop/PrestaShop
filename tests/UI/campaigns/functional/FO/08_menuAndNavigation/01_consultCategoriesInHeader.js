require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');

// Import pages
const homePage = require('@pages/FO/home');

// Import data
const {Categories} = require('@data/demo/categories');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_menuAndNavigation_consultCategoriesInHeader';

let browserContext;
let page;

/*
Go to FO
Check all categories and subcategories links in header
 */

describe('FO - Menu and Navigation : Check categories and subcategories links in header', async () => {
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

  [Categories.clothes, Categories.accessories, Categories.art].forEach((test) => {
    it(`should check category '${test.name}' link`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `check${test.name}Link`, baseContext);

      await homePage.goToCategory(page, test.id);

      const pageTitle = await homePage.getPageTitle(page);
      await expect(pageTitle).to.equal(test.name);
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
      await expect(pageTitle).to.equal(test.args.subcategory.name);
    });
  });
});
