require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productSettingsPage = require('@pages/BO/shopParameters/productSettings');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

const baseContext = 'functional_BO_shopParameters_productSettings_productsGeneral_defaultActivationStatus';

let browserContext;
let page;

/*
Enable default activation status
Check that a new product is online by default
Disable default activation status
Check that a new product is offline by default
 */
describe('BO - Shop Parameters - Product Settings : Enable/Disable default activation status', async () => {
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

  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];

  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      await productSettingsPage.closeSfToolBar(page);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it(`should ${test.args.action} default activation status`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DefaultActivationStatus`,
        baseContext,
      );

      const result = await productSettingsPage.setDefaultActivationStatus(page, test.args.enable);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

      await productSettingsPage.goToSubMenu(
        page,
        productSettingsPage.catalogParentLink,
        productSettingsPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to create product page and check the new product online status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

      await productsPage.goToAddProductPage(page);
      const online = await addProductPage.getOnlineButtonStatus(page);
      await expect(online).to.be.equal(test.args.enable);
    });
  });
});
