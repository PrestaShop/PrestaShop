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
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

// Import FO pages
const productPage = require('@pages/FO/product');
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_shopParameters_productSettings_displayRemainingQuantities';

let browserContext;
let page;

const productData = new ProductFaker({type: 'Standard product', quantity: 2});

const remainingQuantity = 0;
const defaultRemainingQuantity = 3;

/*
Create product quantity 2
Update display remaining quantities to 0
Go to FO product page and check that the product availability is not displayed
Update display remaining quantities to the default value
Go to FO product page and check that the product availability is displayed
 */
describe('BO - Shop Parameters - Product Settings : Display remaining quantities', async () => {
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

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await productsPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);
    const validationMessage = await addProductPage.createEditBasicProduct(page, productData);
    await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await addProductPage.goToSubMenu(
      page,
      addProductPage.shopParametersParentLink,
      addProductPage.productSettingsLink,
    );

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {quantity: remainingQuantity, exist: false, state: 'Displayed'}},
    {args: {quantity: defaultRemainingQuantity, exist: true, state: 'NotDisplayed'}},
  ];

  tests.forEach((test, index) => {
    it(`should update Display remaining quantities to ${test.args.quantity}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `setDisplayRemainingQuantity${index}`, baseContext);

      const result = await productSettingsPage.setDisplayRemainingQuantities(page, test.args.quantity);
      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${test.args.state}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page was not opened').to.be.true;
    });

    it('should search for the product and go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${test.args.state}`, baseContext);

      await homePage.searchProduct(page, productData.name);
      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productData.name);
    });

    it('should check the product availability', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkThatRemainingQuantityIs${test.args.state}`,
        baseContext,
      );

      const lastQuantityIsVisible = await productPage.isAvailabilityQuantityDisplayed(page);
      await expect(lastQuantityIsVisible).to.be.equal(test.args.exist);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${test.args.state}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });
});
