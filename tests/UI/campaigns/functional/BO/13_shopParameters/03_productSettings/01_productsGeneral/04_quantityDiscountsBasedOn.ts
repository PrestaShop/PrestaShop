// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
// Import FO pages
import foProductPage from '@pages/FO/product';
import cartPage from '@pages/FO/cart';

// Import data
import ProductData from '@data/faker/product';
import {ProductAttribute} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_quantityDiscountsBasedOn';

/*
Choose quantity discounts based on 'Products'
Create product with combinations and add a specific price(discount 50% for the first combination)
Add the combinations to the cart and check the price ATI
Choose quantity discounts based on 'Combinations'
Check the cart price ATI
 */
describe('BO - Shop Parameters - Product Settings : Choose quantity discount based on', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const productWithCombinations: ProductData = new ProductData({
    type: 'Standard product',
    price: 20,
    attributes: [
      {
        name: 'color',
        values: ['White', 'Black'],
      },
      {
        name: 'size',
        values: ['S'],
      },
    ],
    quantity: 10,
    specificPrice: {
      attributes: 'Size - S, Color - White',
      discount: 50,
      startingAt: 2,
      reductionType: '%',
    },
  });
  const firstAttributeToChoose: ProductAttribute[] = [
    {
      name: 'color',
      value: 'White',
    },
    {
      name: 'size',
      value: 'S',
    },
  ];
  const secondAttributeToChoose: ProductAttribute[] = [
    {
      name: 'color',
      value: 'Black',
    },
    {
      name: 'size',
      value: 'S',
    },
  ];
  const firstCartTotalATI: number = 30;
  const secondCartTotalATI: number = 40;

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Choose quantity discount based on', async () => {
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

      const createProductMessage = await addProductPage.setAttributesInProduct(
        page,
        productWithCombinations,
      );
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);

      await addProductPage.addSpecificPrices(page, productWithCombinations.specificPrice);
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

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
