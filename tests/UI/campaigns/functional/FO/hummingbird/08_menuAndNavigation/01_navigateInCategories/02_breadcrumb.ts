// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import categoryPage from '@pages/FO/hummingbird/category';

// Import data
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigateInCategories_breadcrumb';

describe('FO - Menu and Navigation : Breadcrumb', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check breadcrumb', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the category Clothes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClothesLink', baseContext);

      await homePage.goToCategory(page, Categories.clothes.id);

      const pageTitle = await categoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.clothes.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb1', baseContext);

      const breadcrumbText = await categoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes');
    });

    it('should go to the subcategory Men', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMenLink', baseContext);

      await homePage.goToSubCategory(page, Categories.clothes.id, Categories.men.id);

      const pageTitle = await homePage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.men.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb2', baseContext);

      const breadcrumbText = await categoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes Men');
    });

    it('should click on clothes link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnClothesLink', baseContext);

      await categoryPage.clickOnBreadCrumbLink(page, 'clothes');

      const pageTitle = await categoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.clothes.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb3', baseContext);

      const breadcrumbText = await categoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Clothes');
    });

    it('should click on Home link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

      await categoryPage.clickOnBreadCrumbLink(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the subcategory stationery', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStationeryLink', baseContext);

      await homePage.goToSubCategory(page, Categories.accessories.id, Categories.stationery.id);

      const pageTitle = await categoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.stationery.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb4', baseContext);

      const breadcrumbText = await categoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Accessories Stationery');
    });

    it('should click on accessories link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnAccessoriesLink', baseContext);

      await categoryPage.clickOnBreadCrumbLink(page, 'accessories');

      const pageTitle = await categoryPage.getPageTitle(page);
      expect(pageTitle).to.equal(Categories.accessories.name);
    });

    it('should check breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb5', baseContext);

      const breadcrumbText = await categoryPage.getBreadcrumbText(page);
      expect(breadcrumbText).to.equal('Home Accessories');
    });

    it('should click on Home link from the breadcrumb', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink2', baseContext);

      await categoryPage.clickOnBreadCrumbLink(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
