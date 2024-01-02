// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';

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

  const productData: ProductData = new ProductData({type: 'standard', status: false});
  const maxSummarySizeValue: number = 5;
  const defaultSummarySizeValue: number = 800;

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
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.productSettingsLink,
        );
        await productSettingsPage.closeSfToolBar(page);

        const pageTitle = await productSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productSettingsPage.pageTitle);
      });

      it(`should update max size of short description to ${test.args.descriptionSize}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateMaxSizeSummaryValue${index}`, baseContext);

        const result = await productSettingsPage.UpdateMaxSizeOfSummary(page, test.args.descriptionSize);
        expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalogProductsPage${index}`, baseContext);

        await productSettingsPage.goToSubMenu(
          page,
          productSettingsPage.catalogParentLink,
          productSettingsPage.productsLink,
        );

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should click on new product button and go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductPage${index}`, baseContext);

        const isModalVisible = await productsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        await productsPage.selectProductType(page, productData.type);
        await productsPage.clickOnAddNewProduct(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      if (test.args.descriptionSize === maxSummarySizeValue) {
        it(`should create a product with a summary more than ${test.args.descriptionSize} characters
      and check the error message`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index}`, baseContext);

          await descriptionTab.setProductDescription(page, productData);

          const errorMessage = await addProductPage.getErrorMessageWhenSummaryIsTooLong(page);
          expect(errorMessage).to.contains(
            addProductPage.errorMessageWhenSummaryTooLong(maxSummarySizeValue),
          );
        });
      } else {
        it(`should create a product with a summary less than ${test.args.descriptionSize} characters`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index}`, baseContext);

          const successMessage = await addProductPage.setProduct(page, productData);
          expect(successMessage).to.equal(addProductPage.successfulUpdateMessage);
        });
      }
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await addProductPage.deleteProduct(page);
      expect(testResult).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
