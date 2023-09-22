// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import productPage from '@pages/FO/product';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigationAndDisplay_displayOnSaleFlag';

/*
Pre-condition:
- Disable new product page
- Create new product with enable 'On sale' flag
- Preview product on check 'On sale' flag
Post-condition:
- Delete created product
- Enable new product page
 */
describe('FO - Navigation and display : Display \'On sale\' flag', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const onSaleProductData: ProductData = new ProductData({
    name: 'On sale product',
    type: 'Standard product',
    onSale: true,
  });

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

  // Pre-condition : Create product with on sale flag
  describe('BO - Create product with enabled flag \'On sale\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage3', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should go to add product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddProductPage2', baseContext);

      await productsPage.goToAddProductPage(page);

      const pageTitle = await addProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(addProductPage.pageTitle);
    });

    it(`create product '${onSaleProductData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await addProductPage.setProduct(page, onSaleProductData);

      const createProductMessage = await addProductPage.displayOnSaleFlag(page, true);
      expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });
  });

  describe('FO - Check \'On sale\' flag', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await addProductPage.previewProduct(page);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(onSaleProductData.name);
    });

    it('should check the discount flag', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountTag', baseContext);

      const flagText = await productPage.getProductTag(page);
      expect(flagText).to.contains('On sale!');
    });
  });

  // Post-condition: Delete created product
  deleteProductTest(onSaleProductData, `${baseContext}_deleteProduct`);

  // Post-condition: Enable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, true, `${baseContext}_enableNewProduct`);
});
