// Import utils
import testContext from '@utils/testContext';

// Import pages
import {homePage} from '@pages/FO/classic/home';
import {categoryPage} from '@pages/FO/classic/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  dataCategories,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_menuAndNavigation_navigateInCategories_breadcrumb';

describe('FO - Menu and Navigation : Breadcrumb', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

    await homePage.goToFo(page);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to the category Clothes', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkClothesLink', baseContext);

    await homePage.goToCategory(page, dataCategories.clothes.id);

    const pageTitle = await categoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.clothes.name);
  });

  it('should check breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb1', baseContext);

    const breadcrumbText = await categoryPage.getBreadcrumbText(page);
    expect(breadcrumbText).to.eq('Home Clothes');
  });

  it('should go to the subcategory Men', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMenLink', baseContext);

    await homePage.goToSubCategory(page, dataCategories.clothes.id, dataCategories.men.id);

    const pageTitle = await homePage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.men.name);
  });

  it('should check breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb2', baseContext);

    const breadcrumbText = await categoryPage.getBreadcrumbText(page);
    expect(breadcrumbText).to.eq('Home Clothes Men');
  });

  it('should click on clothes link from the breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnClothesLink', baseContext);

    await categoryPage.clickOnBreadCrumbLink(page, 'clothes');

    const pageTitle = await categoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.clothes.name);
  });

  it('should check breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb3', baseContext);

    const breadcrumbText = await categoryPage.getBreadcrumbText(page);
    expect(breadcrumbText).to.eq('Home Clothes');
  });

  it('should click on Home link from the breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink', baseContext);

    await categoryPage.clickOnBreadCrumbLink(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should go to the subcategory stationery', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkStationeryLink', baseContext);

    await homePage.goToSubCategory(page, dataCategories.accessories.id, dataCategories.stationery.id);

    const pageTitle = await categoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.stationery.name);
  });

  it('should check breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb4', baseContext);

    const breadcrumbText = await categoryPage.getBreadcrumbText(page);
    expect(breadcrumbText).to.eq('Home Accessories Stationery');
  });

  it('should click on accessories link from the breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnAccessoriesLink', baseContext);

    await categoryPage.clickOnBreadCrumbLink(page, 'accessories');

    const pageTitle = await categoryPage.getPageTitle(page);
    expect(pageTitle).to.equal(dataCategories.accessories.name);
  });

  it('should check breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkBreadcrumb5', baseContext);

    const breadcrumbText = await categoryPage.getBreadcrumbText(page);
    expect(breadcrumbText).to.eq('Home Accessories');
  });

  it('should click on Home link from the breadcrumb', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnHomeLink2', baseContext);

    await categoryPage.clickOnBreadCrumbLink(page, 'en');

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });
});
