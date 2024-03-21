// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/BO/design/hummingbird';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productSettingsPage from '@pages/BO/shopParameters/productSettings';

// Import pages
import homePage from '@pages/FO/hummingbird/home';
import categoryPage from '@pages/FO/hummingbird/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_menuAndNavigation_navigationAndDisplay_pagination';

/*
Pre-condition:
- Install the theme hummingbird
Scenario:
- Go to FO>All products page
- Check the pagination in the bottom of the page
- Click on next then on previous
- Edit products per page number to 6 in BO
- Check the new pagination in FO
- Edit products per page number to 20
- Check the new pagination
Post-condition:
- Reset 'Number of products per page'
- Uninstall the theme hummingbird
 */
describe('FO - Navigation and display : Pagination', async () => {
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

  describe('FO - Pagination next and previous', async () => {
    it('should open the shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openShopPage', baseContext);

      await homePage.goTo(page, global.FO.URL);

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.equal(true);
    });

    it('should check the number of products on the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'numberOfProducts', baseContext);

      const numberOfProducts = await categoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.equal(19);
    });

    it('should check the pagination in the bottom of the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaginationLabel', baseContext);

      const pagesList = await categoryPage.getPagesList(page);
      expect(pagesList).to.contain('1 2 Next');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      await categoryPage.goToNextPage(page);

      const numberOfItems = await categoryPage.getShowingItems(page);
      expect(numberOfItems).to.eq('Showing 13-19 of 19 item(s)');
    });

    it('should check the pagination in the bottom of the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaginationLabel1', baseContext);

      const pagesList = await categoryPage.getPagesList(page);
      expect(pagesList).to.contain('Previous 1 2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      await categoryPage.goToPreviousPage(page);

      const numberOfItems = await categoryPage.getShowingItems(page);
      expect(numberOfItems).to.eq('Showing 1-12 of 19 item(s)');
    });
  });

  describe('BO - Edit products per page number to 6', async () => {
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

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of products per page to 6', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays0', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, 6);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the new pagination', async () => {
    it('should view my shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO1', baseContext);

      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts1', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should check the number of products on the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'numberOfProducts2', baseContext);

      const numberOfProducts = await categoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.eql(19);
    });

    it('should check the pagination in the bottom of the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaginationLabel2', baseContext);

      const pagesList = await categoryPage.getPagesList(page);
      expect(pagesList).to.contain('1 2 3 4 Next');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext1', baseContext);

      await categoryPage.goToNextPage(page);

      const numberOfItems = await categoryPage.getShowingItems(page);
      expect(numberOfItems).to.eq('Showing 7-12 of 19 item(s)');
    });

    it('should check the pagination in the bottom of the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaginationLabel3', baseContext);

      const pagesList = await categoryPage.getPagesList(page);
      expect(pagesList).to.contain('Previous 1 2 3 4 Next');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious1', baseContext);

      await categoryPage.goToPreviousPage(page);

      const numberOfItems = await categoryPage.getShowingItems(page);
      expect(numberOfItems).to.eq('Showing 1-6 of 19 item(s)');
    });
  });

  describe('BO - Edit products per page number to 20', async () => {
    it('should close the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFOPage', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of products per page to 20', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays1', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, 20);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  describe('FO - Check the new pagination', async () => {
    it('should view my shop page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO2', baseContext);

      page = await productSettingsPage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const result = await homePage.isHomePage(page);
      expect(result).to.eq(true);
    });

    it('should go to all products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAllProducts2', baseContext);

      await homePage.changeLanguage(page, 'en');
      await homePage.goToAllProductsPage(page);

      const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
      expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
    });

    it('should check the number of products on the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'numberOfProducts3', baseContext);

      const numberOfProducts = await categoryPage.getNumberOfProducts(page);
      expect(numberOfProducts).to.eql(19);
    });

    it('should check that the pagination label is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaginationLabel4', baseContext);

      const isVisible = await categoryPage.isPagesListVisible(page);
      expect(isVisible).to.eq(false);
    });
  });

  // Post-condition: Reset number of products per page
  describe('POST-TEST : Reset \'Number of products per page\'', async () => {
    it('should go back BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await homePage.closePage(browserContext, page, 0);

      const pageTitle = await productSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productSettingsPage.pageTitle);
    });

    it('should change the number of products per page to 12', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeNumberOfDays2', baseContext);

      const result = await productSettingsPage.setProductsDisplayedPerPage(page, 12);
      expect(result).to.contains(productSettingsPage.successfulUpdateMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest`);
});
