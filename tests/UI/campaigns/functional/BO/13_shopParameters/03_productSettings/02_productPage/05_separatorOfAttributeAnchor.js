require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_separatorOfAttributeAnchor';

let browserContext;
let page;

const productAttributes = ['1', 'size', 's/8', 'color', 'white'];

describe('Update separator of attribute anchor on the product links', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {option: ',', attributesInProductLink: productAttributes.join(',')}},
    {args: {option: '-', attributesInProductLink: productAttributes.join('-')}},
  ];

  tests.forEach((test, index) => {
    it(`should choose the separator option '${test.args.option}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `chooseOption_${index}`, baseContext);

      const result = await productSettingsPage.setSeparatorOfAttributeOnProductLink(
        page,
        test.args.option,
      );

      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should check the attribute separator on the product links in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAttributeSeparator_${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');

      await homePage.searchProduct(page, Products.demo_1.name);
      await searchResultsPage.goToProductPage(page, 1);

      const currentURL = await productPage.getProductPageURL(page);
      await expect(currentURL).to.contains(test.args.attributesInProductLink);

      page = await productPage.closePage(browserContext, page, 0);
    });
  });
});
