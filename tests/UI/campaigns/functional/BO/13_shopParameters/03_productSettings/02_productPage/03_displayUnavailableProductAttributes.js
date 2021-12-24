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

const baseContext = 'functional_BO_shopParameters_productSettings_displayUnavailableProductAttributes';

let browserContext;
let page;

const productData = new ProductFaker(
  {
    type: 'Standard product',
    combinations: {
      color: ['White'],
      size: ['S'],
    },
    quantity: 0,
  },
);

describe('BO - Shop Parameters - Product Settings :  Display unavailable product attributes  '
  + 'on the product page', async () => {
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
    await addProductPage.createEditBasicProduct(page, productData);
    const validationMessage = await addProductPage.setCombinationsInProduct(page, productData);
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
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} Display unavailable product attributes on the product page`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DisplayUnavailableProductAttributes`,
        baseContext,
      );

      const result = await productSettingsPage.setDisplayUnavailableProductAttributesStatus(
        page,
        test.args.enable,
      );

      await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });

    it('should check the unavailable product attributes in FO product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkUnavailableAttribute${index}`, baseContext);

      page = await productSettingsPage.viewMyShop(page);

      await homePage.changeLanguage(page, 'en');

      await homePage.searchProduct(page, productData.name);
      await searchResultsPage.goToProductPage(page, 1);

      const sizeIsVisible = await productPage.isUnavailableProductSizeDisplayed(
        page,
        productData.combinations.size[0],
      );

      await expect(sizeIsVisible).to.be.equal(test.args.enable);

      const colorIsVisible = await productPage.isUnavailableProductColorDisplayed(
        page,
        productData.combinations.color[0],
      );

      await expect(colorIsVisible).to.be.equal(test.args.enable);
    });

    it('should close the page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `closePageAndBackToBO${index}`, baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);

    await productSettingsPage.goToSubMenu(
      page,
      productSettingsPage.catalogParentLink,
      productSettingsPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should delete product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const deleteTextResult = await productsPage.deleteProduct(page, productData);
    await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

    await productsPage.resetFilterCategory(page);
    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });
});
