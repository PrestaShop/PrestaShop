// Import utils
import testContext from '@utils/testContext';

// Import common tests
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {expect} from 'chai';
import {
  type BrowserContext,
  dataCategories,
  foHummingbirdCategoryPage,
  foHummingbirdHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigateInCategories_breadcrumb';

describe('FO - Menu and Navigation - Navigate in Categories : Breadcrumb', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check breadcrumb', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the category Clothes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClothesLink', baseContext);

      await foHummingbirdHomePage.goToCategory(page, dataCategories.clothes.id);

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb1', baseContext);

      const breadcrumbText = await foHummingbirdCategoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes');
    });

    it(`should go to the subcategory "${dataCategories.men.name}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMenLink', baseContext);

      await foHummingbirdHomePage.goToSubCategory(page, dataCategories.clothes.id, dataCategories.men.id);

      const pageTitle = await foHummingbirdHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.men.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb2', baseContext);

      const breadcrumbText = await foHummingbirdCategoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes Men');
    });

    it('should click on clothes link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnClothesLink', baseContext);

      await foHummingbirdCategoryPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.clothes.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb3', baseContext);

      const breadcrumbText = await foHummingbirdCategoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes');
    });

    it('should click on Home link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

      await foHummingbirdCategoryPage.clickOnBreadCrumbLink(page, '/');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the subcategory stationery', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStationeryLink', baseContext);

      await foHummingbirdHomePage.goToSubCategory(page, dataCategories.accessories.id, dataCategories.stationery.id);

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.stationery.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb4', baseContext);

      const breadcrumbText = await foHummingbirdCategoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Accessories Stationery');
    });

    it('should click on accessories link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAccessoriesLink', baseContext);

      await foHummingbirdCategoryPage.clickOnBreadCrumbLink(page, 'accessories');

      const pageTitle = await foHummingbirdCategoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(dataCategories.accessories.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb5', baseContext);

      const breadcrumbText = await foHummingbirdCategoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Accessories');
    });

    it('should click on Home link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink2', baseContext);

      await foHummingbirdCategoryPage.clickOnBreadCrumbLink(page, '/');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest`);
});
