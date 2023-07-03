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
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_maxSizeShortDescription';

/*
Update max size of short description to 5
Check the error message when the description size is more than 5 characters
Go back to the default max size short description
Check the validation message when the description is less than 800 characters
 */
describe('BO - Shop Parameters - Product Settings : Update max size of short description', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: ProductData = new ProductData({type: 'Standard product', status: false});
  const maxSummarySizeValue: number = 5;
  const defaultSummarySizeValue: number = 800;

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

  describe('Update max size of short description', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    const tests = [
      {args: {descriptionSize: maxSummarySizeValue}},
      {args: {descriptionSize: defaultSummarySizeValue}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index + 1}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.productSettingsLink,
        );
        await productSettingsPage.closeSfToolBar(page);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });

      it(`should update max size of short description to ${test.args.descriptionSize}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateMaxSizeSummaryValue${index + 1}`, baseContext);

        const result = await productSettingsPage.UpdateMaxSizeOfSummary(page, test.args.descriptionSize);
        await expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalogProductsPage${index + 1}`, baseContext);

        await productSettingsPage.goToSubMenu(
          page,
          productSettingsPage.catalogParentLink,
          productSettingsPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      if (test.args.descriptionSize === maxSummarySizeValue) {
        it(`should create a product with a summary more than ${test.args.descriptionSize} characters
      and check the error message`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index + 1}`, baseContext);

          await productsPage.goToAddProductPage(page);

          let errorMessage = await addProductPage.createEditBasicProduct(page, productData);
          await expect(errorMessage).to.equal(addProductPage.errorMessage);

          errorMessage = await addProductPage.getErrorMessageWhenSummaryIsTooLong(page);
          await expect(errorMessage).to.equal(
            addProductPage.errorMessageWhenSummaryTooLong(maxSummarySizeValue),
          );
        });
      } else {
        it(`should create a product with a summary less than ${test.args.descriptionSize} characters`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index + 1}`, baseContext);

          await productsPage.goToAddProductPage(page);

          const validationMessage = await addProductPage.createEditBasicProduct(page, productData);
          await expect(validationMessage).to.equal(addProductPage.settingUpdatedMessage);
        });
      }
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
