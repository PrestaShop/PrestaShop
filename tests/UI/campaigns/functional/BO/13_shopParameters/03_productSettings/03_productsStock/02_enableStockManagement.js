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

const baseContext = 'functional_BO_shopParameters_productSettings_productsStock_enableStockManagement';

let browserContext;
let page;

describe('BO - Shop Parameters - Product Settings : Enable/Disable stock management', async () => {
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
    {args: {action: 'disable', enable: false, isQuantityVisible: false}},
    {args: {action: 'enable', enable: true, isQuantityVisible: true}},
  ];

  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage_${index}`, baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.productSettingsLink,
      );

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it(`should ${test.args.action} stock management`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);

      const result = await productSettingsPage.setEnableStockManagementStatus(page, test.args.enable);
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

    it('should go to create product page and check the existence of quantity input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIsQuantityInput${test.args.action}`, baseContext);

      await productsPage.goToAddProductPage(page);
      const isVisible = await addProductPage.isQuantityInputVisible(page);
      await expect(isVisible).to.equal(test.args.isQuantityVisible);
    });
  });
});
