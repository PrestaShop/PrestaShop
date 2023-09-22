// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard/index';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import productsPageV2 from '@pages/BO/catalog/productsV2';
import createProductsPageV2 from '@pages/BO/catalog/productsV2/add';
import productsPageV1 from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';
import {isNewProductPageEnabledByDefault} from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'functional_BO_advancedParameters_newAndExperimentalFeatures_enableDisableExperimentalProductV2';

/*
- Enable/Disable new product page
- Go to products page > new product and check it
 */
describe('BO - Advanced Parameters - New & Experimental Features : Enable/Disable new product page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  // Depending on the initial status we don't test in the same order
  if (isNewProductPageEnabledByDefault()) {
    testDisableProductPage();
    testEnableProductPage();
  } else {
    testEnableProductPage();
    testDisableProductPage();
  }

  function testEnableProductPage() {
    describe('Enable new product page', async () => {
      it('should go to \'Advanced Parameters > New & Experimental Features\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.featureFlagLink,
        );
        await featureFlagPage.closeSfToolBar(page);

        const pageTitle = await featureFlagPage.getPageTitle(page);
        expect(pageTitle).to.contains(featureFlagPage.pageTitle);
      });

      it('should enable New product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableNewProductPage', baseContext);

        const successMessage = await featureFlagPage.setFeatureFlag(page, featureFlagPage.featureFlagProductPageV2, true);
        expect(successMessage).to.be.contain(featureFlagPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageV2', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPageV2.closeSfToolBar(page);

        const pageTitle = await productsPageV2.getPageTitle(page);
        expect(pageTitle).to.contains(productsPageV2.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible = await productsPageV2.clickOnNewProductButton(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should choose \'Standard product\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

        await productsPageV2.selectProductType(page, 'standard');
        await productsPageV2.clickOnAddNewProduct(page);

        const pageTitle = await createProductsPageV2.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPageV2.pageTitle);
      });
    });
  }

  function testDisableProductPage() {
    describe('Disable new product page', async () => {
      it('should go back to \'Advanced Parameters > New & Experimental Features\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage2', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.featureFlagLink,
        );
        await featureFlagPage.closeSfToolBar(page);

        const pageTitle = await featureFlagPage.getPageTitle(page);
        expect(pageTitle).to.contains(featureFlagPage.pageTitle);
      });

      it('should disable New product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableNewProductPage', baseContext);

        const successMessage = await featureFlagPage.setFeatureFlag(page, featureFlagPage.featureFlagProductPageV2, false);
        expect(successMessage).to.be.contain(featureFlagPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsV1Page', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPageV1.closeSfToolBar(page);

        const pageTitle = await productsPageV1.getPageTitle(page);
        expect(pageTitle).to.contains(productsPageV1.pageTitle);
      });

      it('should go to Add product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddProductV1Page', baseContext);

        await productsPageV1.goToAddProductPage(page);

        const createProductTitle = await addProductPage.getPageTitle(page);
        expect(createProductTitle).to.contains(addProductPage.pageTitle);
      });
    });
  }
});
