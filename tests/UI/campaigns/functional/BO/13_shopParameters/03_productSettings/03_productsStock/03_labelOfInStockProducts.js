require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');

const baseContext = 'functional_BO_shopParameters_productSettings_productsStock_labelOfInStockProducts';

let browserContext;
let page;

describe('BO - Shop Parameters - Product Settings : Update label of in-stock products', async () => {
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

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });
  const tests = [
    {args: {label: 'Product is available', labelToCheck: 'Product is available', exist: true}},
    {args: {label: ' ', labelToCheck: '', exist: false}},
  ];

  tests.forEach((test, index) => {
    it(`should set '${test.args.label}' in Label of in-stock products input`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateLabelOfInStockProducts_${index}`, baseContext);

      const result = await productSettingsPage.setLabelOfInStockProducts(page, test.args.label);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should check the label of in-stock product in FO product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkLabelInStock_${index}`,
        baseContext,
      );

      page = await productSettingsPage.viewMyShop(page);

      await homePage.goToProductPage(page, 1);

      const isVisible = await productPage.isAvailabilityQuantityDisplayed(page);
      await expect(isVisible).to.be.equal(test.args.exist);

      const availabilityLabel = await productPage.getProductAvailabilityLabel(page);
      await expect(availabilityLabel).to.contains(test.args.labelToCheck);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
