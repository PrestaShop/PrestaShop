import testContext from '@utils/testContext';
import {expect} from 'chai';

import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import {
  boDiscountsPage,
  boDashboardPage,
  boFeatureFlagPage,
  boLoginPage,
  // Utils
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_discount_createDiscount';

describe('BO - Discount - Create discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Pre-condition: Enable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, true, `${baseContext}_preTest`);

  // 2 - Create discount form new page
  describe('Create discount', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    // Create discount
    it('should click on \'create discount\' button and check new discount modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewDiscountByType', baseContext);

      await boDiscountsPage.addNewDiscountByType(page, 'product_level');

      // Check the form page is open
      //const pageTitle = await boCreateDiscountsFormPage.getPageTitle(page);
      //expect(pageTitle).to.contains(boCreateDiscountsFormPage.pageTitle);
    });
  });

  // 3 - Pre-condition: Enable improved_shipment
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_preTest`);
});
