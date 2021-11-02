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
const foProductPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_shopParameters_productSettings_productsGeneral_quantityDiscountsBasedOn';

let browserContext;
let page;
const productWithCombinations = new ProductFaker(
  {
    type: 'Standard product',
    price: 20,
    combinations: {
      color: ['White', 'Black'],
      size: ['S'],
    },
    quantity: 10,
    specificPrice: {
      combinations: 'Size - S, Color - White',
      discount: 50,
      startingAt: 2,
      reductionType: '%',
    },
  },
);

const firstAttributeToChoose = {color: 'White', size: 'S'};
const secondAttributeToChoose = {color: 'Black', size: 'S'};

const firstCartTotalATI = 30;
const secondCartTotalATI = 40;
let numberOfProducts = 0;

/*
Choose quantity discounts based on 'Products'
Create product with combinations and add a specific price(discount 50% for the first combination)
Add the combinations to the cart and check the price ATI
Choose quantity discounts based on 'Combinations'
Check the cart price ATI
 */
describe('BO - Shop Parameters - Product Settings : Choose quantity discount based on', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage1', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.productSettingsLink,
    );

    await productSettingsPage.closeSfToolBar(page);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  it('should choose quantity discounts based on \'Products\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseQuantityDiscountsBasedOnProducts', baseContext);

    const result = await productSettingsPage.chooseQuantityDiscountsBasedOn(page, 'Products');
    await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
  });

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await productSettingsPage.goToSubMenu(
      page,
      productSettingsPage.catalogParentLink,
      productSettingsPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterProducts', baseContext);

    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create product with combinations and add a specific price', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);
    await addProductPage.createEditBasicProduct(page, productWithCombinations);

    const createProductMessage = await addProductPage.setCombinationsInProduct(
      page,
      productWithCombinations,
    );

    await addProductPage.addSpecificPrices(page, productWithCombinations.specificPrice);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview product and check price ATI in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProductAndCheckPriceATI', baseContext);

    page = await addProductPage.previewProduct(page);

    await foProductPage.addProductToTheCart(page, 1, firstAttributeToChoose, false);
    await foProductPage.addProductToTheCart(page, 1, secondAttributeToChoose, true);

    const priceATI = await cartPage.getATIPrice(page);
    await expect(priceATI).to.equal(firstCartTotalATI);

    page = await cartPage.closePage(browserContext, page, 0);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage2', baseContext);

    await addProductPage.goToSubMenu(
      page,
      addProductPage.shopParametersParentLink,
      addProductPage.productSettingsLink,
    );

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
  });

  it('should choose quantity discounts based on \'Combinations\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseQuantityDiscountsBasedOnCombinations', baseContext);

    const result = await productSettingsPage.chooseQuantityDiscountsBasedOn(page, 'Combinations');
    await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
  });

  it('should view my shop and check ATI price in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'ViewMyShopAndCheckPriceATI', baseContext);

    page = await productSettingsPage.viewMyShop(page);

    await foProductPage.goToCartPage(page);
    const priceATI = await cartPage.getATIPrice(page);
    await expect(priceATI).to.equal(secondCartTotalATI);
  });

  it('should close the page and go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closePageAndBackToBO', baseContext);

    page = await cartPage.closePage(browserContext, page, 0);

    const pageTitle = await productSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
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

  it('should delete product from dropDown menu', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const deleteTextResult = await productsPage.deleteProduct(page, productWithCombinations);
    await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);

    const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProductsAfterDelete).to.equal(numberOfProducts);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });
});
